<template>
  <!-- eslint-disable max-len -->
  <div class="container mt-6 mx-auto">
    <ul>
      <li
        v-for="ability in abilities"
        :key="ability.id"
        class="px-2 py-2 first:border-t border-b border-gray-400 odd:bg-gray-200"
      >
        <Ability
          :ability="ability"
          class="ml-4"
        />
      </li>
    </ul>
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
    abilities() {
      return this.$store.state.user.ability.abilities || [];
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
      this.$store.dispatch('user/ability/browse')
        .catch((error) => {
          console.log(error);
        });
    },
  }
};
</script>
