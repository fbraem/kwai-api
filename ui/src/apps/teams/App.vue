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
import typeStore from './store/type';

export default {
  beforeCreate() {
    this.$store.registerModule('team', teamStore);
    this.$store.registerModule(['team', 'type'], typeStore);
  },
  beforeDestroy() {
    this.$store.dispatch('team/type/reset');
    this.$store.unregisterModule(['team', 'type']);
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
