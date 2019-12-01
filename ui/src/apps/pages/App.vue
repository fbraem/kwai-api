<template>
  <div>
    <router-view name="hero"></router-view>
    <router-view></router-view>
    <CategoryCards :categories="categories" />
  </div>
</template>

<script>
import store from './store';

import CategoryCards from '@/apps/categories/components/CategoryCards';

export default {
  components: {
    CategoryCards
  },
  computed: {
    categories() {
      return this.$store.state.category.all;
    }
  },
  beforeCreate() {
    this.$store.registerModule('page', store);
  },
  beforeDestroy() {
    this.$store.dispatch('page/reset');
    this.$store.unregisterModule('page');
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      if (to.meta.active) {
        vm.$store.dispatch('page/set', to.meta.active);
      }
    });
  }
};
</script>
