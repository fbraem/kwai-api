<template>
  <!-- eslint-disable max-len -->
  <div class="uk-grid-small uk-grid-margin-small uk-grid-stack" uk-grid>
    <div class="uk-width-1-1@m">
      <div class="uk-margin uk-text-center uk-child-width-1-1 uk-child-width-1-2@s uk-child-width-1-3@m uk-grid-medium uk-grid-match uk-flex-center" uk-height-match=".uk-card" uk-grid>
        <Card v-for="category in categories" :key="category.id" :category="category" />
      </div>
    </div>
  </div>
</template>

<script>
import Card from './components/Card.vue';

import messages from './lang';

import categoryStore from '@/stores/categories';
import registerModule from '@/stores/mixin';

export default {
  components: {
    Card
  },
  i18n: messages,
  mixins: [
    registerModule(
      {
        category: categoryStore
      }
    ),
  ],
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
