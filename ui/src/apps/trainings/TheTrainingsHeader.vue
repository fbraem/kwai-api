<template>
  <Header
    :title="$t('training.events.title')"
    :toolbar="toolbar"
    :logo="logo"
    :route= "{ name: 'trainings.index' }"
  />
</template>

<script>
import Training from '@/models/trainings/Training';
import Coach from '@/models/trainings/Coach';
import Team from '@/models/Team';

import Header from '@/components/Header';

import messages from './lang';

export default {
  components: {
    Header
  },
  i18n: messages,
  computed: {
    logo() {
      const category
        = this.$store.getters['category/categoryApp'](this.$route.meta.app);
      return category.icon_picture;
    },
    toolbar() {
      const buttons = [];
      if (this.$can('create', Training.type())) {
        buttons.push({
          icon: 'fas fa-calendar-plus',
          route: {
            name: 'trainings.definitions.browse'
          }
        });
        buttons.push({
          icon: 'fas fa-plus',
          route: {
            name: 'trainings.create'
          }
        });
      }
      if (this.$can('manage', Team.type())) {
        buttons.push({
          icon: 'fas fa-users',
          route: {
            name: 'teams.browse'
          }
        });
      }
      if (this.$can('manage', Coach.type())) {
        buttons.push({
          icon: 'fas fa-user-tie',
          route: {
            name: 'trainings.coaches'
          }
        });
      }
      return buttons;
    }
  }
};
</script>
