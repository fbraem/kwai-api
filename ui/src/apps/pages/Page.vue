<template>
  <div class="page-container">
    <div>
      <slot></slot>
    </div>
    <div>
      <CategoryList
        v-if="categories"
        :categories="categories"
      />
    </div>
  </div>
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
  },
  created() {
    this.$store.dispatch('category/browse');
  }
};
</script>
