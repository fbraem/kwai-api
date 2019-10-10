<template>
  <div class="container mx-auto mt-3">
    <Spinner v-if="$wait.is('seasons.browse')" />
    <Alert
      v-if="noSeasons"
      type="warning"
    >
      {{ $t('no_seasons') }}
    </Alert>
    <div v-else>
      <table class="border-collapse w-full">
        <thead>
          <tr>
            <th class="py-4 px-4 font-bold border-b bg-gray-200"></th>
            <th class="py-4 px-4 font-bold border-b bg-gray-200 text-left">
              {{ $t('form.season.name.label') }}
            </th>
            <th class="py-4 px-4 font-bold border-b bg-gray-200 text-left">
              {{ $t('period') }}
            </th>
            <th class="py-4 px-4 font-bold border-b bg-gray-200"></th>
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
