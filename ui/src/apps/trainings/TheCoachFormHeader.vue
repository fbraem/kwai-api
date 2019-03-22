<template>
  <Header
    :title="$t('training.coaches.title')"
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
  i18n: messages,
  props: {
    creating: {
      type: Boolean,
      required: true
    }
  },
  computed: {
    subtitle() {
      return this.$t(this.creating ?
        'training.coaches.create' : 'training.coaches.update'
      );
    }
  }
};
</script>
