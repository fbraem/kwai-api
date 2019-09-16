<template>
  <div class="page-container">
    <table class="kwai-table kwai-table-divider">
      <tr>
        <th>{{ $t('rules.name') }}</th>
      </tr>
      <tr
        v-for="ability in abilities"
        :key="ability.id"
      >
        <Ability :ability="ability" />
      </tr>
    </table>
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
