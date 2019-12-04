<template>
  <div class="container mx-auto mt-3">
    <Spinner v-if="$wait.is('seasons.browse')" />
    <div v-else>
      <Alert
        v-if="noSeasons"
        type="warning"
      >
        {{ $t('no_seasons') }}
      </Alert>
      <table class="table">
        <thead>
          <tr>
            <th></th>
            <th>
              {{ $t('form.season.name.label') }}
            </th>
            <th>
              {{ $t('period') }}
            </th>
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
</template>

<script>
import messages from './lang';

import Spinner from '@/components/Spinner';
import Alert from '@/components/Alert';
import SeasonRow from './TheSeasonRow';

export default {
  i18n: messages,
  components: {
    Spinner,
    SeasonRow,
    Alert
  },
  computed: {
    seasons() {
      return this.$store.state.season.all;
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
