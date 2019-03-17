<template>
  <Header
    :title="$t('teams')"
    :subtitle="subtitle"
    :toolbar="toolbar"
  />
</template>

<script>
import messages from './lang';

import Header from '@/components/Header';

export default {
  i18n: messages,
  components: {
    Header
  },
  computed: {
    team() {
      return this.$store.getters['team/team'](this.$route.params.id);
    },
    subtitle() {
      return this.team ? this.team.name : null;
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
      return buttons;
    }
  }
};
</script>
