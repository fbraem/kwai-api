<template>
  <div class="news">
    <router-view name="hero"></router-view>
    <router-view></router-view>
  </div>
</template>

<script>
import store from './store';

export default {
  beforeCreate() {
    this.$store.registerModule('category', store);
  },
  beforeDestroy() {
    this.$store.dispatch('category/reset');
    this.$store.unregisterModule('category');
  },
  beforeRouteEnter(to, from, next) {
    next(async vm => {
      await store.dispatch('category/read', { id: to.params.id });
      const category = store.getters['category/category'](to.params.id);
      if (category.app) {
        return {
          path: '/' + category.app
        };
      }
    });
  }
};
</script>
