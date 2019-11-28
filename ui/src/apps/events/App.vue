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
    this.$store.registerModule('events', store);
  },
  beforeDestroy() {
    this.$store.dispatch('events/reset');
    this.$store.unregisterModule('events');
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      if (to.meta.active) {
        vm.$store.dispatch('events/set', to.meta.active);
      }
    });
  }
};
</script>
