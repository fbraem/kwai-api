<template>
  <div class="news">
    <router-view name="hero"></router-view>
    <router-view></router-view>
  </div>
</template>

<script>
import newsStore from '@/apps/news/store';
import pageStore from '@/stores/pages';

export default {
  beforeCreate() {
    this.$store.registerModule(['category', 'news'], newsStore);
    this.$store.registerModule(['category', 'page'], pageStore);
  },
  beforeDestroy() {
    this.$store.dispatch('category/news/reset');
    this.$store.unregisterModule(['category', 'news']);
    // this.$store.dispatch('category/page/reset');
    this.$store.unregisterModule(['category', 'page']);
  },
  beforeRouteEnter(to, from, next) {
    if (to.name === 'category.read') {
      next(async vm => {
        await vm.$store.dispatch('category/read', { id: to.params.id });
        const category = vm.$store.getters['category/category'](to.params.id);
        if (category.app) {
          return {
            path: '/' + category.app
          };
        }
      });
    } else {
      next();
    }
  }
};
</script>
