<template>
  <!-- eslint-disable max-len -->
  <div>
    <div v-if="$wait.is('teams.browse')" class="uk-flex-center" uk-grid>
      <div class="uk-text-center">
        <i class="fas fa-spinner fa-2x fa-spin"></i>
      </div>
    </div>
    <div v-else class="uk-width-1-2@xl" uk-grid>
      <div v-if="teams && teams.length == 0">
        {{ $t('no_teams') }}
      </div>
      <table v-else class="uk-table uk-table-small uk-table-divider uk-table-middle">
        <tr>
          <th>{{ $t('name') }}</th>
          <th>{{ $t('season') }}</th>
          <th class="uk-table-shrink">{{ $t('members') }}</th>
          <th class="uk-table-shrink"></th>
        </tr>
        <tr v-for="team in teams" :key="team.id">
          <td>
            <router-link :to="{ name: 'teams.read', params: { id : team.id} }">{{ team.name }}</router-link>
          </td>
          <td>
            <router-link v-if="team.season" :to="{ name: 'seasons.read', params: { id : team.season.id} }">{{ team.season.name }}</router-link>
          </td>
          <td>
            {{ team.members_count }}
          </td>
          <td>
            <router-link class="uk-icon-button uk-link-reset" v-if="$team.isAllowed('update', team)" :to="{ name : 'teams.update', params : { id : team.id } }">
              <i class="fas fa-edit uk-text-muted"></i>
            </router-link>
          </td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
import messages from './lang';

export default {
  i18n: messages,
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
