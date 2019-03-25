<template>
  <div>
    <Spinner v-if="$wait.is('seasons.browse')" />
    <div
      v-else
      uk-grid
    >
      <div
        v-if="noSeasons"
        class="uk-alert uk-alert-warning"
      >
        {{ $t('no_seasons') }}
      </div>
      <div
        class="uk-width-1-1"
        v-else
      >
        <table class="uk-table uk-table-striped">
          <tr>
            <th></th>
            <th>{{ $t('form.season.name.label') }}</th>
            <th>{{ $t('form.season.start_date.label') }}</th>
            <th>{{ $t('form.season.end_date.label') }}</th>
            <th></th>
          </tr>
          <SeasonRow
            v-for="season in seasons"
            :key="season.id"
            :season="season"
          />
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import messages from './lang';

import Spinner from '@/components/Spinner';
import SeasonRow from './TheSeasonRow';

export default {
  i18n: messages,
  components: {
    Spinner,
    SeasonRow
  },
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
