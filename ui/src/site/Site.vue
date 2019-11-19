<template>
  <!-- eslint-disable max-len -->
  <div class="site">
    <router-view></router-view>
  </div>
</template>

<script>
import store from './store';

export default {
  beforeCreate() {
    console.log('bc - site');
    this.$store.registerModule('site', store);
  },
  beforeDestroy() {
    console.log('bd - site');
    this.$store.unregisterModule('site');
  }
};
</script>
