<template>
  <Header
    :title="$t('category')"
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
  props: {
    /**
     * Is the form for creating or updating a category?
     */
    creating: {
      type: Boolean,
      required: true
    }
  },
  i18n: messages,
  computed: {
    subtitle() {
      console.log(this.creating);
      if (this.creating) {
        return this.$t('create');
      }
      return this.$t('update');
    }
  }
};
</script>
