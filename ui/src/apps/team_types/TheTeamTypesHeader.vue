<template>
  <Header
    :title="$t('types')"
    :toolbar="toolbar"
  />
</template>

<script>
import TeamType from '@/models/TeamType';

import messages from './lang';

import Header from '@/components/Header';

export default {
  components: {
    Header
  },
  i18n: messages,
  computed: {
    toolbar() {
      const buttons = [];
      if (this.$can('create', TeamType.type())) {
        buttons.push({
          icon: 'fas fa-plus',
          route: {
            name: 'team_types.create'
          }
        });
      }
      return buttons;
    }
  }
};
</script>
