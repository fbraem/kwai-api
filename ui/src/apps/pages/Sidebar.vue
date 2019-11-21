<template>
  <CategoryList
    v-if="categories"
    :categories="categories"
  />
</template>

<script>
import messages from './lang';

import CategoryList from '@/apps/categories/components/CategoryList.vue';

export default {
  i18n: messages,
  components: {
    CategoryList
  },
  computed: {
    categories() {
      return this.$store.state.category.categories;
    }
  }
};
</script>
