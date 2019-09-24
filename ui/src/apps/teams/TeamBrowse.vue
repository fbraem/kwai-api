<template>
  <div class="page-container">
    <Spinner v-if="$wait.is('teams.browse')" />
    <div
      v-else
      style="grid-column: span 2;"
    >
      <div v-if="teams && teams.length == 0">
        {{ $t('no_teams') }}
      </div>
      <div v-else>
        <table class="kwai-table kwai-table-small kwai-table-divider kwai-table-middle">
          <tr>
            <th>{{ $t('name') }}</th>
            <th>{{ $t('season') }}</th>
            <th class="kwai-table-shrink">{{ $t('members') }}</th>
            <th class="kwai-table-shrink"></th>
          </tr>
          <tr
            v-for="team in teams"
            :key="team.id"
          >
            <td class="kwai-middle">
              <router-link
                :to="{ name: 'teams.read', params: { id : team.id} }"
              >
                {{ team.name }}
              </router-link>
            </td>
            <td class="kwai-middle">
              <router-link
                v-if="team.season"
                :to="{ name: 'seasons.read', params: { id : team.season.id} }"
              >
                {{ team.season.name }}
              </router-link>
            </td>
            <td class="kwai-middle">
              {{ team.members_count }}
            </td>
            <td>
              <router-link
                v-if="$can('update', team)"
                class="kwai-icon-button"
                :to="{ name : 'teams.update', params : { id : team.id } }"
              >
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

import Spinner from '@/components/Spinner';

export default {
  i18n: messages,
  components: {
    Spinner
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
