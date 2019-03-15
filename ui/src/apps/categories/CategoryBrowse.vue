<template>
  <!-- eslint-disable max-len -->
  <div class="uk-grid-small uk-grid-margin-small uk-grid-stack" uk-grid>
    <div class="uk-width-1-1@m">
      <div
        class="uk-margin uk-text-center uk-child-width-1-2@s uk-child-width-1-3@m uk-grid-medium uk-grid-match uk-flex-center"
        uk-height-match=".uk-card"
        uk-grid
      >
        <CategoryCard
          v-for="category in categories"
          :key="category.id"
          :category="category"
        />
      </div>
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
