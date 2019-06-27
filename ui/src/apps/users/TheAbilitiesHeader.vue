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
    toolbar() {
      const buttons = [];
      if (this.$can('create', Ability.type())) {
        buttons.push({
          icon: 'fas fa-plus',
          route: {
            name: 'users.abilities.create'
          }
        });
      }
      return buttons;
    }
  }
};
</script>
