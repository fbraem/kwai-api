<template>
  <!-- eslint-disable max-len -->
  <div class="page-container">
    <Spinner
      v-if="$wait.is('seasons.read')"
      style="grid-column: span 2;"
    />
    <div v-if="season" style="grid-area: page-content">
      <h3 class="kwai-header-line">
        <span>{{ $t('season') }}</span>
      </h3>
      <table class="kwai-table kwai-table-divider">
        <tr>
          <th>
            {{ $t('form.season.name.label') }}
          </th>
          <td class="kwai-table-expand">
            {{ season.name }}
          </td>
        </tr>
        <tr>
          <th>
            {{ $t('form.season.start_date.label') }}
          </th>
          <td>
            {{ season.formatted_start_date }}
          </td>
        </tr>
        <tr>
          <th>
            {{ $t('form.season.end_date.label') }}
          </th>
          <td>
            {{ season.formatted_end_date }}
          </td>
        </tr>
        <tr>
          <th>
            {{ $t('form.season.remark.label') }}
          </th>
          <td>
            {{ season.remark }}
          </td>
        </tr>
      </table>
      <div v-if="season.active">
        <i class="fas fa-check"></i>
        <span style="vertical-align:bottom">
          &nbsp;&nbsp;{{ $t('active_message') }}
        </span>
      </div>
    </div>
    <div
      v-if="season"
      style="grid-area:page-sidebar; display: flex; flex-direction: column;"
    >
      <h3 class="kwai-header-line">
        <span>{{ $t('teams') }}</span>
      </h3>
      <table
        v-if="season.teams"
        class="kwai-table kwai-table-divider"
        >
        <tr
          v-for="team in season.teams"
          :key="team.id"
        >
          <td>
            <router-link
              :to="{ 'name' : 'teams.read', params : { id : team.id } }"
            >
              {{ team.name }}
            </router-link>
          </td>
        </tr>
      </table>
      <div
        v-else
        class="warning:kwai-alert">
        {{ $t('no_teams') }}
      </div>
      <div
        class="kwai-buttons"
        style="align-self: flex-end; margin-top: 20px;"
      >
        <router-link
          class="kwai-icon-button"
          :to="{ 'name' : 'teams.browse' }"
        >
          <i class="fas fa-list"></i>
        </router-link>
        <router-link
          v-if="canCreateTeam"
          class="kwai-icon-button"
          :to="{ 'name' : 'teams.create', params : { season : season.id } }"
        >
          <i class="fas fa-plus"></i>
        </router-link>
      </div>
    </div>
    <div
      style="grid-column: span 2;"
      v-if="! $wait.is('seasons.read') && !season"
      class="danger:kwai-alert"
    >
      {{ $t('season_not_found') }}
    </div>
  </div>
</template>

<script>
import Team from '@/models/Team';

import messages from './lang';

import Spinner from '@/components/Spinner';

export default {
  components: {
    Spinner
  },
  i18n: messages,
  computed: {
    canCreateTeam() {
      return this.$can('create', Team.type());
    },
    season() {
      return this.$store.getters['season/season'](this.$route.params.id);
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
