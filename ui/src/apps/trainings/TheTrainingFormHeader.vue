<template>
  <!-- eslint-disable max-len -->
  <Header
    :title="$t('training.events.title')"
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
      return this.creating
        ? this.$t('training.events.create')
        : this.$t('training.events.update');
    }
  }
};
</script>
