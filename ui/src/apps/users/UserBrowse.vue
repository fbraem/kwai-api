<template>
  <div class="mt-6">
    <Spinner v-if="$wait.is('users.browse')" />
    <div v-else
      class="flex flex-wrap"
    >
      <div
        v-for="user in users"
        :key="user.id"
        class="p-3 w-full sm:w-1/2"
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
