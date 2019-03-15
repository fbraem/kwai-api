<template>
  <div>
    <Spinner v-if="$wait.is('teams.browse')" />
    <div
      v-else
      uk-grid
    >
      <div v-if="teams && teams.length == 0">
        {{ $t('no_teams') }}
      </div>
      <div v-else>
        <table class="uk-table uk-table-small uk-table-divider uk-table-middle">
          <tr>
            <th>{{ $t('name') }}</th>
            <th>{{ $t('season') }}</th>
            <th class="uk-table-shrink">{{ $t('members') }}</th>
            <th class="uk-table-shrink"></th>
          </tr>
          <tr
            v-for="team in teams"
            :key="team.id"
          >
            <td>
              <router-link
                :to="{ name: 'teams.read', params: { id : team.id} }"
              >
                {{ team.name }}
              </router-link>
            </td>
            <td>
              <router-link
                v-if="team.season"
                :to="{ name: 'seasons.read', params: { id : team.season.id} }"
              >
                {{ team.season.name }}
              </router-link>
            </td>
            <td>
              {{ team.members_count }}
            </td>
            <td>
              <router-link
                v-if="$can('update', team)"
                class="uk-icon-button uk-link-reset"
                :to="{ name : 'teams.update', params : { id : team.id } }"
              >
                <i class="fas fa-edit uk-text-muted"></i>
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
