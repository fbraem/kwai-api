<template>
  <Header
    :title="$t('training.definitions.title')"
    :toolbar="toolbar"
  />
</template>

<script>
import Definition from '@/models/trainings/Definition';

import Header from '@/components/Header';

import messages from './lang';

export default {
  components: {
    Header
  },
  i18n: messages,
  computed: {
    toolbar() {
      const buttons = [];
      if (this.$can('create', Definition.type())) {
        buttons.push({
          icon: 'fas fa-plus',
          route: {
            name: 'trainings.definitions.create'
          }
        });
      }
      return buttons;
    }
  }
};
</script>
