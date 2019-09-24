<template>
  <div class="page-container">
    <div style="grid-area: page-sidebar">
      <UserCard
        v-if="user"
        :user="user"
      />
    </div>
    <div style="grid-area: page-content">
      <h1>{{ $t('rules.groups') }}</h1>
      <p class="kwai-text-meta">
        {{ $t('rules.groups_info') }}
      </p>
      <table v-if="user" class="kwai-table kwai-table-divider">
        <tr>
          <th>{{ $t('rules.name') }}</th>
        </tr>
        <tr
          v-for="ability in userAbilities"
          :key="ability.id"
        >
          <UserAbility
            :ability="ability"
            :remove="true"
            @remove="removeAbility"
          />
        </tr>
      </table>
      <h1>Available groups</h1>
      <table v-if="user" class="kwai-table kwai-table-divider">
        <tr>
          <th>{{ $t('rules.name') }}</th>
        </tr>
        <tr
          v-for="ability in availableAbilities"
          :key="ability.id"
        >
          <UserAbility
            :ability="ability"
            :add="true"
            @add="addAbility"
          />
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
import UserCard from './components/UserCard';
import UserAbility from './TheUserAbility';

import messages from './lang';

export default {
  i18n: messages,
  components: {
    UserCard, UserAbility
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
    availableAbilities() {
      return this.abilities.filter(x => !this.userAbilities.includes(x));
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
