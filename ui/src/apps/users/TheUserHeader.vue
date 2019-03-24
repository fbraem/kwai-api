<template>
  <Header
    :title="$t('user_mgmt')"
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
    user() {
      return this.$store.getters['user/user'](
        this.$route.params.id
      );
    },
    subtitle() {
      if (this.user) {
        var name = this.user.name;
        if (name.length === 0) return this.user.email;
        return name;
      }
      return '';
    },
    toolbar() {
      const buttons = [];
/*
      if (this.$can('update', User.type())) {
        buttons.push({
          icon: 'fas fa-edit',
          route: {
            name: 'users.update'
          }
        });
      }
*/
      return buttons;
    }
  }
};
</script>
