<template>
  <Header
    :title="$t('categories')"
    :subtitle="subtitle"
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
  props: {
    creating: {
      type: Boolean,
      required: true
    }
  },
  computed: {
    subtitle() {
      if (this.creating) return this.$t('create_category');
      return this.$t('update_category');
    }
  }
};
</script>
