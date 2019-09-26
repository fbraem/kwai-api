<template>
  <Header
    :title="$t('seasons')"
    :toolbar="toolbar"
  />
</template>

<script>
import Season from '@/models/Season';

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
      if (this.$can('create', Season.type())) {
        buttons.push({
          icon: 'fas fa-plus',
          route: {
            name: 'seasons.create'
          }
        });
      }
      return buttons;
    }
  }
};
</script>
