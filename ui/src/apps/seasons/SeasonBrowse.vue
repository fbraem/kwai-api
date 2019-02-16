<template>
  <!-- eslint-disable max-len -->
  <div>
    <div v-if="$wait.is('seasons.browse')" class="uk-flex-center" uk-grid>
      <div class="uk-text-center">
        <i class="fas fa-spinner fa-2x fa-spin"></i>
      </div>
    </div>
    <div v-else class="uk-child-width-1-1" uk-grid>
      <div v-if="noSeasons" class="uk-alert uk-alert-warning">
        {{ $t('no_seasons') }}
      </div>
      <div v-else>
        <table class="uk-table uk-table-striped">
          <tr>
            <th></th>
            <th>{{ $t('form.season.name.label') }}</th>
            <th>{{ $t('form.season.start_date.label') }}</th>
            <th>{{ $t('form.season.end_date.label') }}</th>
            <th></th>
          </tr>
          <tr v-for="season in seasons" :key="season.id">
            <td>
              <i class="fas fa-check" v-if="season.active"></i>
            </td>
            <td>
              <router-link
                :to="{ name: 'seasons.read', params: { id: season.id} }">
                {{ season.name }}
              </router-link>
            </td>
            <td>
              {{ season.formatted_start_date }}
            </td>
            <td>
              {{ season.formatted_end_date }}
            </td>
            <td>
              <router-link v-if="$season.isAllowed('update', season)"
                class="uk-icon-button"
                style="margin-top:-10px"
                :to="{ name: 'seasons.update', params: { id: season.id } }">
                <i class="fas fa-edit"></i>
              </router-link>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import messages from './lang';

import seasonStore from '@/stores/seasons';
import registerModule from '@/stores/mixin';

export default {
  i18n: messages,
  mixins: [ registerModule({ season: seasonStore }) ],
  computed: {
    seasons() {
      return this.$store.state.season.seasons;
    },
    noSeasons() {
      return this.seasons && this.seasons.length === 0;
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
    fetchData() {
      this.$store.dispatch('season/browse');
    }
  }
};
</script>
