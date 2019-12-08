<template>
  <Header :title="$t('teams')" />
</template>

<script>
import messages from './lang';

import Header from '@/components/Header';

export default {
  i18n: messages,
  components: {
    Header
  }
};
</script>
