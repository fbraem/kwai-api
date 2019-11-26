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
    this.$store.registerModule('news', store);
  },
  beforeDestroy() {
    this.$store.dispatch('news/reset');
    this.$store.unregisterModule('news');
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      if (to.meta.active) {
        vm.$store.dispatch('news/set', to.meta.active);
      }
    });
  }
};
</script>
