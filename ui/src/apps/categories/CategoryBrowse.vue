<template>
  <!-- eslint-disable max-len -->
  <div class="icon-card-container" style="padding:20px">
    <div
      v-for="category in categories"
      :key="category.id"
      class="icon-card-item"
    >
      <CategoryCard :category="category" />
    </div>
  </div>
</template>

<script>
import CategoryCard from './components/CategoryCard.vue';

import messages from './lang';

/**
 * Page for showing all categories
 */
export default {
  components: {
    CategoryCard
  },
  i18n: messages,
  computed: {
    categories() {
      return this.$store.state.category.categories;
    },
    noData() {
      return this.categories && this.categories.length === 0;
    }
  },
  beforeRouteEnter(to, from, next) {
    next(async(vm) => {
      await vm.fetchData();
      next();
    });
  },
  async beforeRouteUpdate(to, from, next) {
    await this.fetchData();
    next();
  },
  methods: {
    fetchData() {
      this.$store.dispatch('category/browse');
    }
  }
};
</script>
