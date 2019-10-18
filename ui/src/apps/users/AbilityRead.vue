<template>
  <div class="container mt-6 mx-auto">
    <Ability v-if="ability" :ability="ability" />
  </div>
</template>

<script>
import Ability from './TheUserAbility';

import messages from './lang';

export default {
  components: {
    Ability
  },
  i18n: messages,
  computed: {
    ability() {
      return this.$store.getters['user/ability/ability'](this.$route.params.id);
    }
  },
  beforeRouteEnter(to, from, next) {
    next(async(vm) => {
      await vm.fetchData(to.params.id);
      next();
    });
  },
  async beforeRouteUpdate(to, from, next) {
    await this.fetchData(to.params.id);
    next();
  },
  methods: {
    fetchData(id) {
      this.$store.dispatch('user/ability/read', {
        id
      })
        .catch((error) => {
          console.log(error);
        });
    },
  }
};
</script>
