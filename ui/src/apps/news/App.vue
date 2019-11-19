<template>
  <div class="news">
    <router-view name="hero"></router-view>
    <router-view></router-view>
  </div>
</template>

<script>
import store from './store';
import categoryStore from '@/apps/categories/store';

export default {
  beforeCreate() {
    this.$store.registerModule('news', store);
    this.$store.registerModule(['news', 'category'], categoryStore);
  },
  beforeDestroy() {
    this.$store.dispatch('news/reset');
    this.$store.unregisterModule('news');
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      if (to.meta.active) {
        vm.$store.dispatch('news/active', to.meta.active);
      }
    });
  }
};
</script>
