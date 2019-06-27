<template>
  <div uk-grid>
    <div class="uk-width-1-1@s uk-width-1-3@m">
      <UserCard
        v-if="user"
        :user="user"
      />
    </div>
    <div class="uk-width-1-1@s uk-width-2-3@m">
      <h1 class="uk-heading-small">{{ $t('rules.groups') }}</h1>
      <p class="uk-text-meta">
        {{ $t('rules.groups_info') }}
      </p>
      <table v-if="user" class="uk-table uk-table-divider">
        <tr>
          <th>{{ $t('rules.name') }}</th>
        </tr>
        <tr
          v-for="ability in abilities"
          :key="ability.id"
        >
          <UserAbility :ability="ability" />
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
    abilities() {
      return this.user.abilities || [];
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
      this.$store.dispatch('user/readWithAbilities', { id })
        .catch((error) => {
          console.log(error);
        });
    },
    showRules() {
      console.log('show');
    }
  }
};
</script>
