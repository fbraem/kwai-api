<template>
  <Header
    :title="$t('training.definitions.title')"
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
    definition() {
      return this.$store.getters['training/definition/definition'](
        this.$route.params.id
      );
    },
    subtitle() {
      if (this.definition) return this.definition.name;
      return '';
    },
    toolbar() {
      const buttons = [{
        icon: 'fas fa-list',
        route: {
          name: 'trainings.definitions.browse'
        }
      }];
      if (this.definition && this.$can('update', this.definition)) {
        buttons.push({
          icon: 'fas fa-edit',
          route: {
            name: 'trainings.definitions.update',
            id: this.definition.id
          }
        });
      }
      return buttons;
    }
  }
};
</script>
