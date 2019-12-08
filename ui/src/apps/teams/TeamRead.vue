<template>
  <AdminPage :toolbar="toolbar">
    <router-view name="team_information"></router-view>
  </AdminPage>
</template>

<script>
import Team from '@/models/Team';

import AdminPage from '@/components/AdminPage';

export default {
  components: {
    AdminPage
  },
  computed: {
    team() {
      return this.$store.state.team.active;
    },
    toolbar() {
      const buttons = [
        {
          icon: 'fas fa-list',
          route: {
            name: 'teams.browse'
          }
        },
      ];
      if (this.$route.name === 'teams.read') {
        if (this.team && this.$can('update', this.team)) {
          buttons.push({
            icon: 'fas fa-edit',
            route: {
              name: 'teams.update',
              params: {
                id: this.team.id
              }
            }
          });
        }
        if (this.$can('create', Team.type())) {
          buttons.push({
            icon: 'fas fa-plus',
            route: {
              name: 'teams.create',
            }
          });
        }
      }
      return buttons;
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
    fetchData(params) {
      try {
        this.$store.dispatch('team/getMembers', {
          id: params.id
        });
      } catch (error) {
        console.log(error);
      }
    }
  }
};
</script>
