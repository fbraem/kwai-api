<template>
  <Header
    :title="$t('members')"
    :subtitle="$t('upload')"
  />
</template>

<script>
import messages from './lang';
import Header from '@/components/Header';

export default {
  components: {
    Header
  },
  i18n: messages,
};
</script>
