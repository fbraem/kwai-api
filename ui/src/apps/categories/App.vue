<template>
  <div class="news">
    <router-view name="hero"></router-view>
    <router-view></router-view>
  </div>
</template>

<script>
import newsStore from '@/apps/news/store';
import pageStore from '@/apps/pages/store';

export default {
  beforeCreate() {
    this.$store.registerModule(['category', 'news'], newsStore);
    this.$store.registerModule(['category', 'page'], pageStore);
  },
  beforeDestroy() {
    this.$store.dispatch('category/news/reset');
    this.$store.unregisterModule(['category', 'news']);
    this.$store.dispatch('category/page/reset');
    this.$store.unregisterModule(['category', 'page']);
  }
};
</script>
