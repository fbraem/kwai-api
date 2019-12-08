<template>
  <div>
    <router-view name="hero"></router-view>
    <div class="container mx-auto p-4 lg:p-6">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
import teamStore from './store/team';
import teamCategoryStore from './store/category';
import seasonStore from '@/apps/seasons/store';

export default {
  beforeCreate() {
    this.$store.registerModule('team', teamStore);
    this.$store.registerModule(['team', 'category'], teamCategoryStore);
    this.$store.registerModule(['team', 'season'], seasonStore);
  },
  beforeDestroy() {
    this.$store.dispatch('team/season/reset');
    this.$store.unregisterModule(['team', 'season']);
    this.$store.dispatch('team/category/reset');
    this.$store.unregisterModule(['team', 'category']);
    this.$store.dispatch('team/reset');
    this.$store.unregisterModule('team');
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      if (to.meta.active) {
        vm.$store.dispatch('team/set', to.meta.active);
      }
    });
  }
};
</script>
