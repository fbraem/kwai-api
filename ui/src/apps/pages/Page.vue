<template>
  <div uk-grid>
    <div class="uk-width-1-1 uk-width-2-3@m uk-width-4-5@xl">
      <slot></slot>
    </div>
    <div class="uk-width-1-1 uk-width-1-3@m uk-width-1-5@xl">
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
