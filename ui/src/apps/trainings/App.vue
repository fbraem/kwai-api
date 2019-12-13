<template>
  <div>
    <div class="container mx-auto p-4 lg:p-6">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
import trainingStore from './store/training';
import definitionStore from './store/definition';
import coachStore from './store/coach';
import teamStore from '@/apps/teams/store/team';
import seasonStore from '@/apps/seasons/store';
import memberStore from '@/apps/members/store';

export default {
  beforeCreate() {
    this.$store.registerModule('training', trainingStore);
    this.$store.registerModule(['training', 'coach'], coachStore);
    this.$store.registerModule(['training', 'definition'], definitionStore);
    this.$store.registerModule(['training', 'season'], seasonStore);
    this.$store.registerModule(['training', 'team'], teamStore);
    this.$store.registerModule(['training', 'member'], memberStore);
  },
  destroyed() {
    this.$store.unregisterModule(['training', 'definition']);
    this.$store.unregisterModule(['training', 'coach']);
    this.$store.unregisterModule(['training', 'season']);
    this.$store.unregisterModule(['training', 'team']);
    this.$store.unregisterModule(['training', 'member']);
    this.$store.unregisterModule('training');
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      if (to.meta.active) {
        vm.$store.dispatch('training/set', to.meta.active);
      }
    });
  }
};
</script>
