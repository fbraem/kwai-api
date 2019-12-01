<template>
  <div class="news">
    <router-view name="hero"></router-view>
    <router-view></router-view>
    <CategoryCards :categories="categories" />
  </div>
</template>

<script>
import store from './store';
import CategoryCards from '@/apps/categories/components/CategoryCards';

export default {
  components: {
    CategoryCards
  },
  computed: {
    categories() {
      return this.$store.state.category.all;
    }
  },
  beforeCreate() {
    this.$store.registerModule('news', store);
  },
  beforeDestroy() {
    this.$store.dispatch('news/reset');
    this.$store.unregisterModule('news');
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      if (to.meta.active) {
        vm.$store.dispatch('news/set', to.meta.active);
      }
    });
  }
};
</script>
