<template>
  <!-- eslint-disable max-len -->
  <div>
    <AreYouSure id="delete-season" :yes="$t('delete')" :no="$t('cancel')" @sure="deleteSeason">
      {{ $t('are_you_sure') }}
    </AreYouSure>
    <div v-if="$wait.is('seasons.read')" class="uk-flex-center" uk-grid>
      <div class="uk-text-center">
        <i class="fas fa-spinner fa-2x fa-spin"></i>
      </div>
    </div>
    <div v-else-if="season" class="uk-grid uk-grid-divider" uk-grid>
      <div class="uk-width-1-1@s uk-width-1-2@m">
        <div>
          <h3 class="uk-heading-line"><span>{{ $t('season') }}</span></h3>
          <table class="uk-table uk-table-divider">
            <tr>
              <th class="uk-text-top">{{ $t('form.season.name.label') }}</th>
              <td class="uk-table-expand">{{ season.name }}</td>
            </tr>
            <tr>
              <th class="uk-text-top">{{ $t('form.season.start_date.label') }}</th>
              <td>{{ season.formatted_start_date }}</td>
            </tr>
            <tr>
              <th class="uk-text-top">{{ $t('form.season.end_date.label') }}</th>
              <td>{{ season.formatted_end_date }}</td>
            </tr>
            <tr>
              <th class="uk-text-top">{{ $t('form.season.remark.label') }}</th>
              <td>{{ season.remark }}</td>
            </tr>
          </table>
          <div v-if="season.active">
            <i class="fas fa-check"></i>
            <span style="vertical-align:bottom">&nbsp;&nbsp;{{ $t('active_message') }}</span>
          </div>
        </div>
      </div>
      <div class="uk-width-1-1@s uk-width-1-2@m" v-if="season">
        <div>
          <h3 class="uk-heading-line"><span>{{ $t('teams') }}</span></h3>
          <table v-if="season.teams" class="uk-table uk-table-divider">
            <tr v-for="team in season.teams" :key="team.id">
              <td>
                <router-link :to="{ 'name' : 'teams.read', params : { id : team.id } }">
                  {{ team.name }}
                </router-link>
              </td>
            </tr>
          </table>
          <div v-else class="uk-alert uk-alert-warning">
            {{ $t('no_teams') }}
          </div>
        </div>
        <div class="uk-flex uk-flex-right">
          <div>
            <router-link class="uk-icon-button" :to="{ 'name' : 'teams.browse' }">
              <i class="fas fa-list"></i>
            </router-link>
          </div>
          <div class="uk-margin-small-left">
            <router-link v-if="$team.isAllowed('create')" class="uk-icon-button" :to="{ 'name' : 'teams.create', params : { season : season.id } }">
              <i class="fas fa-plus"></i>
            </router-link>
          </div>
        </div>
      </div>
    </div>
    <div v-else class="uk-width-1-1 uk-alert uk-alert-danger">
      {{ $t('season_not_found') }}
    </div>
  </div>
</template>

<script>
import messages from './lang';

import AreYouSure from '@/components/AreYouSure.vue';

export default {
  components: {
    AreYouSure
  },
  i18n: messages,
  computed: {
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
    },
    deleteSeason() {
      console.log('delete');
    }
  }
};
</script>
