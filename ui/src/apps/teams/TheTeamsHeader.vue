<template>
  <Header
    :title="$t('teams')"
    :toolbar="toolbar"
  />
</template>

<script>
import Team from '@/models/Team';

import messages from './lang';

import Header from '@/components/Header';

export default {
  i18n: messages,
  components: {
    Header
  },
  computed: {
    toolbar() {
      const buttons = [];
      if (this.$can('create', Team.type())) {
        buttons.push({
          icon: 'fas fa-plus',
          route: {
            name: 'teams.create'
          }
        });
      }
      return buttons;
    }
  }
};
</script>
