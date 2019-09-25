<template>
  <div class="page-container">
    <Spinner v-if="$wait.is('seasons.browse')" />
    <div style="grid-column: span 2;">
      <div
        v-if="noSeasons"
        class="warning:kwai-alert"
      >
        {{ $t('no_seasons') }}
      </div>
      <div v-else>
        <table class="kwai-table kwai-table-striped kwai-table-responsive">
          <thead>
            <tr>
              <th></th>
              <th>{{ $t('form.season.name.label') }}</th>
              <th>{{ $t('period') }}</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <SeasonRow
              v-for="season in seasons"
              :key="season.id"
              :season="season"
            />
          </tbody>
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
