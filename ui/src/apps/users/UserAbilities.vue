<template>
  <!-- eslint-disable max-len -->
  <Page>
    <template slot="sidebar">
      <UserCard
        v-if="user"
        :user="user"
      />
    </template>
    <div>
      <h1>{{ $t('rules.groups') }}</h1>
      <p class="text-sm">
        {{ $t('rules.groups_info') }}
      </p>
      <ul v-if="user">
        <li
          v-for="ability in userAbilities"
          :key="ability.id"
          class="px-2 py-2 first:border-t border-b border-gray-400 odd:bg-gray-200"
        >
          <UserAbility
            :ability="ability"
            :remove="true"
            @remove="removeAbility"
            class="ml-4"
          />
        </li>
      </ul>
      <h1>Available groups</h1>
      <ul v-if="user">
        <li
          v-for="ability in availableAbilities"
          :key="ability.id"
          class="px-2 py-2 first:border-t border-b border-gray-400 odd:bg-gray-200"
        >
          <UserAbility
            :ability="ability"
            :add="true"
            @add="addAbility"
            class="ml-4"
          />
        </li>
      </ul>
    </div>
  </Page>
</template>

<script>
import UserCard from './components/UserCard';
import UserAbility from './TheUserAbility';
import Page from '@/components/Page';

import messages from './lang';

export default {
  i18n: messages,
  components: {
    UserCard, UserAbility, Page
  },
  data() {
    return {
      show: {
      }
    };
  },
  computed: {
    user() {
      return this.$store.getters['user/user'](this.$route.params.id);
    },
    userAbilities() {
      return this.user.abilities || [];
    },
    abilities() {
      return this.$store.state.user.ability.abilities || [];
    },
    /**
      Return all abilities which are not yet attached to this user
    */
    availableAbilities() {
      const userAbilities = this.userAbilities.map(x => x.name);
      return this.abilities.filter(x => !userAbilities.includes(x.name));
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
    async fetchData(id) {
      await this.$store.dispatch('user/readWithAbilities', { id })
        .catch((error) => {
          console.log(error);
        });
      await this.$store.dispatch('user/ability/browse')
        .catch((error) => {
          console.log(error);
        });
    },
    addAbility(ability) {
      this.$store.dispatch('user/attachAbility', {
        user: this.user,
        ability
      });
    },
    removeAbility(ability) {
      this.$store.dispatch('user/detachAbility', {
        user: this.user,
        ability
      });
    }
  }
};
</script>
