<template>
  <Header
    :title="$t('categories')"
    :toolbar="toolbar"
  />
</template>

<script>
import Category from '@/models/Category';
import messages from './lang';

import Header from '@/components/Header';

/**
 * Component for header of the browse page
 */
export default {
  components: {
    Header
  },
  i18n: messages,
  computed: {
    toolbar() {
      const buttons = [];
      if (this.$can('create', Category.type())) {
        buttons.push({
          icon: 'fas fa-plus',
          route: {
            name: 'categories.create'
          }
        });
      }
      return buttons;
    }
  }
};
</script>
