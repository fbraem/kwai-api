<template>
  <Header
    :title="$t('training.coaches.title')"
    :toolbar="toolbar"
  />
</template>

<script>
import Coach from '@/models/trainings/Coach';

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
      if (this.$can('create', Coach.type())) {
        buttons.push({
          icon: 'fas fa-plus',
          route: {
            name: 'trainings.coaches.create'
          }
        });
      }
      return buttons;
    }
  }
};
</script>
