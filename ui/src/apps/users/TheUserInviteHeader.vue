<template>
  <Header
    :title="$t('user_mgmt')"
    :subtitle="$t('invite')"
  />
</template>

<script>
import Header from '@/components/Header';
import messages from './lang';

export default {
  components: {
    Header
  },
  i18n: messages
};
</script>
