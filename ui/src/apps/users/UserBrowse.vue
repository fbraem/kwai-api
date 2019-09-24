<template>
  <div class="page-container">
    <Spinner v-if="$wait.is('users.browse')" />
    <div style="grid-column: span 2; display: flex; flex-wrap: wrap;"
      v-else
    >
      <div
        v-for="user in users"
        :key="user.id"
        class="user-item"
      >
          <UserCard :user="user" />
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
@import "@/site/scss/_mq.scss";

.user-item {
  margin-top: 0px !important;
  padding-bottom: 20px;
  padding-left: 20px;

  @include mq($until: tablet) {
    width: 100%;
  }
  @include mq($from: tablet) {
    width: 50%;
  }
}
</style>

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
