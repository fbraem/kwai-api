<template>
  <!-- eslint-disable max-len -->
  <Page>
    <Spinner
      v-if="$wait.is('seasons.read')"
      style="grid-column: span 2;"
    />
    <div v-if="season" style="grid-area: page-content">
      <h3 class="header-line">
        {{ $t('season') }}
      </h3>
      <Attributes :attributes="attributes" />
      <Alert v-if="season.active" :icon="false">
        <i class="fas fa-check"></i>
        &nbsp;&nbsp;{{ $t('active_message') }}
      </Alert>
    </div>
    <template
      v-if="season"
      slot="sidebar"
      style="grid-area:page-sidebar;"
    >
      <div class="flex flex-col">
        <h3 class="header-line">
          {{ $t('teams') }}
        </h3>
        <ul v-if="season.teams">
          <li
            v-for="team in season.teams"
            :key="team.id"
            class="p-2"
          >
            <router-link
              :to="{ 'name' : 'teams.read', params : { id : team.id } }"
            >
              {{ team.name }}
            </router-link>
          </li>
        </ul>
        <Alert
          v-else
          type="warning">
          {{ $t('no_teams') }}
        </Alert>
        <div class="self-end mt-4">
          <router-link
            class="icon-button text-gray-700 hover:bg-gray-300"
            :to="{ 'name' : 'teams.browse' }"
          >
            <i class="fas fa-list"></i>
          </router-link>
          <router-link
            v-if="canCreateTeam"
            class="icon-button text-gray-700 hover:bg-gray-300"
            :to="{ 'name' : 'teams.create', params : { season : season.id } }"
          >
            <i class="fas fa-plus"></i>
          </router-link>
        </div>
      </div>
    </template>
    <div
      style="grid-column: span 2;"
      v-if="! $wait.is('seasons.read') && !season"
    >
      <Alert type="danger">
        {{ $t('season_not_found') }}
      </Alert>
    </div>
  </Page>
</template>

<script>
import Team from '@/models/Team';

import messages from './lang';

import Page from '@/components/Page';
import Attributes from '@/components/Attributes';
import Spinner from '@/components/Spinner';
import Alert from '@/components/Alert';

export default {
  components: {
    Spinner, Alert, Page, Attributes
  },
  i18n: messages,
  computed: {
    canCreateTeam() {
      return this.$can('create', Team.type());
    },
    season() {
      return this.$store.getters['season/season'](this.$route.params.id);
    },
    attributes() {
      return {
        name: {
          label: this.$t('form.season.name.label'),
          value: this.season.name
        },
        start_date: {
          label: this.$t('form.season.start_date.label'),
          value: this.season.formatted_start_date
        },
        end_date: {
          label: this.$t('form.season.end_date.label'),
          value: this.season.formatted_end_date
        },
        remark: {
          label: this.$t('form.season.remark.label'),
          value: this.season.remark
        }
      };
    }
  },
  beforeRouteEnter(to, from, next) {
    next(async(vm) => {
      await vm.fetchData(to.params);
      next();
    });
  },
  async beforeRouteUpdate(to, from, next) {
    await this.fetchData(to.params);
    next();
  },
  methods: {
    fetchData(params) {
      this.$store.dispatch('season/read', { id: params.id })
        .catch((error) => {
          console.log(error);
        });
    }
  }
};
</script>
