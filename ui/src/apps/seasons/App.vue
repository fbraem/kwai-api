<template>
  <div>
    <router-view name="hero"></router-view>
    <router-view></router-view>
  </div>
</template>

<script>
import store from './store';

export default {
  beforeCreate() {
    this.$store.registerModule('season', store);
  },
  beforeDestroy() {
    this.$store.dispatch('season/reset');
    this.$store.unregisterModule('season');
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      if (to.meta.active) {
        vm.$store.dispatch('season/set', to.meta.active);
      }
    });
  }
};
</script>
