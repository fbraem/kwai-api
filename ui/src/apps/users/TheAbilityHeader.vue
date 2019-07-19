<template>
  <Header
    :title="$t('rules.title')"
    :toolbar="toolbar"
  />
</template>

<script>
import Header from '@/components/Header';

import Ability from '@/models/users/Ability';

import messages from './lang';

export default {
  components: {
    Header
  },
  i18n: messages,
  computed: {
    ability() {
      return this.$store.getters['user/ability'](
        this.$route.params.id
      );
    },
    toolbar() {
      const buttons = [];
      if (this.$can('update', Ability.type())) {
        buttons.push({
          icon: 'fas fa-edit',
          route: {
            name: 'users.abilities.update'
          }
        });
      }
      return buttons;
    }
  }
};
</script>
