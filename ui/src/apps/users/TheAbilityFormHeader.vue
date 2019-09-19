<template>
  <Header
    :title="$t('rules.title')"
    :subtitle="subtitle"
  />
</template>

<script>
import messages from './lang';

import Header from '@/components/Header';

export default {
  i18n: messages,
  components: {
    Header
  },
  props: {
    creating: {
      type: Boolean,
      required: true
    }
  },
  computed: {
    subtitle() {
      return this.creating ? this.$t('rules.create') : this.$t('rules.update');
    }
  }
};
</script>
