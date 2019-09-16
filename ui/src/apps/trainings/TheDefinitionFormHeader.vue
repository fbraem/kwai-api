<template>
  <Header
    :title="$t('training.definitions.title')"
    :subtitle="subtitle"
  />
</template>

<script>
import messages from './lang';

import Header from '@/components/Header';

export default {
  i18n: messages,
  props: {
    creating: {
      type: Boolean,
      required: true
    }
  },
  components: {
    Header
  },
  computed: {
    subtitle() {
      return this.creating
        ? this.$t('training.definitions.create')
        : this.$t('training.definitions.update');
    }
  }
};
</script>
