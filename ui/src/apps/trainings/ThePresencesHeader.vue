<template>
  <Header
    :title="$t('training.presences.title')"
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
      return this.$store.getters['training/training'](
        this.$route.params.id
      );
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
        buttons.push({
          icon: 'fas fa-calendar-day',
          route: {
            name: 'trainings.read',
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
