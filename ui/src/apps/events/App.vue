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
    this.$store.registerModule('event', store);
  },
  beforeDestroy() {
    this.$store.dispatch('event/reset');
    this.$store.unregisterModule('event');
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      if (to.meta.active) {
        vm.$store.dispatch('event/set', to.meta.active);
      }
    });
  }
};
</script>
