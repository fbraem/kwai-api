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
