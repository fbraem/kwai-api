<template>
  <Header
    :title="$t('rules.title')"
    :toolbar="toolbar"
  />
</template>

<script>
import Header from '@/components/Header';

import RuleGroup from '@/models/users/RuleGroup';

import messages from './lang';

export default {
  components: {
    Header
  },
  i18n: messages,
  computed: {
    toolbar() {
      const buttons = [];
      if (this.$can('create', RuleGroup.type())) {
        buttons.push({
          icon: 'fas fa-plus',
          route: {
            name: 'users.rule_groups.create'
          }
        });
      }
      return buttons;
    }
  }
};
</script>
