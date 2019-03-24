<template>
  <div>
    <Spinner v-if="$wait.is('users.browse')" />
    <div
      v-else
      uk-grid
    >
      <div
        v-for="user in users"
        :key="user.id"
        class="uk-width-1-2@m"
      >
          <UserCard :user="user" />
      </div>
    </div>
  </div>
</template>

<script>
import Spinner from '@/components/Spinner';

import messages from './lang';

import UserCard from './components/UserCard.vue';

export default {
  i18n: messages,
  components: {
    Spinner,
    UserCard
  },
  computed: {
    noAvatarImage() {
      return require('@/apps/users/images/no_avatar.png');
    },
    users() {
      return this.$store.state.user.users;
    },
  },
  beforeRouteEnter(to, from, next) {
    next(async(vm) => {
      await vm.fetchData();
      next();
    });
  },
  async beforeRouteUpdate(to, from, next) {
    await this.fetchData();
    next();
  },
  methods: {
    fetchData() {
      this.$store.dispatch('user/browse')
        .catch((error) => {
          console.log(error);
        });
    }
  }
};
</script>
