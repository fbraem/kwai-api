<template>
  <div class="flex flex-wrap justify-center">
    <Spinner v-if="$wait.is('teams.browse')" />
    <div v-else-if="teams && teams.length == 0">
      {{ $t('no_teams') }}
    </div>
    <div
      v-else
      class="flex flex-wrap"
    >
      <div
        v-for="team in teams"
        :key="team.id"
        class="p-4 w-full md:w-1/2"
      >
        <TeamCard :team="team" />
      </div>
    </div>
  </div>
</template>

<script>
import messages from './lang';

import Spinner from '@/components/Spinner';
import TeamCard from './TeamCard';

export default {
  i18n: messages,
  components: {
    Spinner,
    TeamCard
  },
  computed: {
    teams() {
      return this.$store.state.team.teams;
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
      this.$store.dispatch('team/browse');
    }
  }
};
</script>
