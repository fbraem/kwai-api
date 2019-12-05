<template>
  <div class="flex flex-wrap justify-center">
    <Spinner v-if="$wait.is('teams.browse')" />
    <div v-else-if="teams && teams.length == 0">
      {{ $t('no_teams') }}
    </div>
    <table
      v-else
      class="table table-striped"
    >
      <thead>
        <tr>
          <th
            scope="col"
            class="w-3/5"
          >
            {{ $t('name') }}
          </th>
          <th
            scope="col"
            class="w-1/5"
          >
            {{ $t('season') }}
          </th>
          <th
            scope="col"
            class="w-1/5 text-right"
          >
            {{ $t('members') }}
          </th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="team in teams"
          :key="team.id"
        >
          <th scope="row">
            <router-link :to="{ name: 'teams.read', params: { id: team.id } }">
              {{ team.name }}
            </router-link>
          </th>
          <td>
            <router-link
              v-if="team.season"
              :to="{ name: 'seasons.read', params: { id : team.season.id} }"
            >
              {{ team.season.name }}
            </router-link>
          </td>
          <td class="text-right">
            {{ team.members_count }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import messages from './lang';

import Spinner from '@/components/Spinner';

export default {
  i18n: messages,
  components: {
    Spinner
  },
  computed: {
    teams() {
      return this.$store.state.team.all;
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
