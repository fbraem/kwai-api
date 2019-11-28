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
    this.$store.registerModule('member', store);
  },
  beforeDestroy() {
    this.$store.dispatch('member/reset');
    this.$store.unregisterModule('member', store);
  }
};
</script>
