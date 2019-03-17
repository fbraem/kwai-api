<template>
  <Header
    :title="$t('teams')"
    :subtitle="subtitle"
  />
</template>

<script>
import Header from '@/components/Header';

import messages from './lang';

export default {
  props: {
    creating: {
      type: Boolean,
      required: true
    }
  },
  i18n: messages,
  components: {
    Header
  },
  computed: {
    subtitle() {
      return this.$t(this.creating ? 'create' : 'update');
    }
  }
};
</script>
