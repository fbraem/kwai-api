<template>
  <div class="news">
    <router-view name="hero"></router-view>
    <router-view></router-view>
  </div>
</template>

<script>
export default {
  beforeRouteEnter(to, from, next) {
    next(async vm => {
      await vm.$store.dispatch('category/read', { id: to.params.id });
      const category = vm.$store.getters['category/category'](to.params.id);
      if (category.app) {
        return {
          path: '/' + category.app
        };
      }
    });
  }
};
</script>
