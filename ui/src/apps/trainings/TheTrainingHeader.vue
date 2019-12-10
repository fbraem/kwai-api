<template>
  <Header
    :title="$t('training.events.title')"
    :toolbar="toolbar"
  />
</template>

<script>
import Header from '@/components/Header';

import messages from './lang';

export default {
  components: {
    Header
  },
  i18n: messages,
  computed: {
    training() {
      return this.$store.state.training.active;
    },
    toolbar() {
      const buttons = [];
      if (this.training) {
        buttons.push({
          icon: 'fas fa-list',
          route: {
            name: 'trainings.browse',
            params: {
              year: this.training.event.start_date.year(),
              month: this.training.event.start_date.month() + 1
            }
          }
        });
      }
      if (this.training && this.$can('update', this.training)) {
        buttons.push({
          icon: 'fas fa-edit',
          route: {
            name: 'trainings.update',
            params: {
              id: this.training.id
            }
          }
        });
      }
      return buttons;
    }
  }
};
</script>
