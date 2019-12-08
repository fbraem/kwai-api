<template>
  <Header
    :title="$t('categories')"
  />
</template>

<script>
import TeamCategory from '@/models/TeamCategory';

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
      if (this.$can('create', TeamCategory.type())) {
        buttons.push({
          icon: 'fas fa-plus',
          route: {
            name: 'team_categories.create'
          }
        });
      }
      return buttons;
    }
  }
};
</script>
