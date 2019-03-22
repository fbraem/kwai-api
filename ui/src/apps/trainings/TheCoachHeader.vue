<template>
  <Header
    :title="$t('training.coaches.title')"
    :subtitle="subtitle"
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
    coach() {
      return this.$store.getters['training/coach/coach'](
        this.$route.params.id
      );
    },
    subtitle() {
      if (this.coach) return this.coach.name;
      return '';
    },
    toolbar() {
      const buttons = [
        {
          icon: 'fas fa-list',
          route: {
            name: 'trainings.coaches'
          }
        },
      ];
      if (this.coach && this.$can('update', this.coach)) {
        buttons.push({
          icon: 'fas fa-edit',
          route: {
            name: 'trainings.coaches.update',
            params: {
              id: this.coach.id
            }
          }
        });
      }
      return buttons;
    }
  }
};
</script>
