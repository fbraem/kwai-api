<template>
  <div>
    <router-view name="hero"></router-view>
    <router-view></router-view>
  </div>
</template>

<script>
import newsStore from '@/apps/news/store';

export default {
  beforeCreate() {
    console.log('bc - app');
    this.$store.registerModule(['site', 'news'], newsStore);
  },
  beforeDestroy() {
    console.log('bd - app');
    this.$store.unregisterModule(['site', 'news']);
  },
  beforeRouteLeave(to, from, next) {
    if (to.name === 'news.story') {
      to.meta.active = this.$store.getters['site/news/story'](to.params.id);
    }
    next();
  }
};
</script>
