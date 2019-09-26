<template>
  <Header
    :title="$t('news')"
    :subtitle="subtitle"
  />
</template>

<script>
import Header from '@/components/Header';

import messages from './lang';

export default {
  components: {
    Header
  },
  props: {
    creating: {
      type: Boolean,
      required: true
    }
  },
  i18n: messages,
  computed: {
    subtitle() {
      if (this.creating) return this.$t('create');
      return this.$t('update');
    }
  }
};
</script>
