<template>
  <Header
    :title="$t('page')"
    :subtitle="subtitle"
  />
</template>

<script>
import messages from './lang';
import Header from '@/components/Header';

/**
 * Component for form header
 */
export default {
  components: {
    Header
  },
  i18n: messages,
  props: {
    /**
     * Creating or updating a page?
     */
    creating: {
      type: Boolean,
      required: true
    }
  },
  computed: {
    subtitle() {
      if (this.creating) return this.$t('create');
      return this.$t('update');
    }
  }
};
</script>
