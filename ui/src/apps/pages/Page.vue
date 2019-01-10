<template>
  <section class="uk-section uk-section-small uk-container uk-container-expand">
    <div uk-grid>
      <div class="uk-width-1-1 uk-width-2-3@m uk-width-4-5@xl">
        <slot></slot>
      </div>
      <div class="uk-width-1-1 uk-width-1-3@m uk-width-1-5@xl">
        <ListCategories :categories="categories" />
      </div>
    </div>
  </section>
</template>

<script>
import messages from './lang';

import registerModule from '@/stores/mixin';
import categoryStore from '@/stores/categories';

import ListCategories from '@/apps/categories/components/List.vue';

export default {
  i18n: messages,
  components: {
    ListCategories
  },
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
    }
  },
  created() {
    this.$store.dispatch('category/browse');
  }
};
</script>
