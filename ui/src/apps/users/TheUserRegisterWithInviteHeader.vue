<template>
  <Header
    :title="$t('invitation.title')"
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
};
</script>
