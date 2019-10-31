<template>
  <Header
    v-if="member"
    :title="$t('members')"
    :subtitle="member.person.name"
    :toolbar="toolbar"
  >
  </Header>
</template>

<script>
import messages from './lang';

import Header from '@/components/Header';

export default {
  components: {
    Header
  },
  i18n: messages,
  computed: {
    member() {
      return this.$store.getters['member/member'](this.$route.params.id);
    },
    toolbar() {
      const buttons = [];
      return buttons;
    }
  },
};
</script>
