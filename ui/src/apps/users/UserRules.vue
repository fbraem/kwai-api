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
          v-for="rule_group in rule_groups"
          :key="rule_group.id"
        >
          <RuleGroup :rule_group="rule_group" />
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
import UserCard from './components/UserCard';
import RuleGroup from './TheUserRuleGroup';

import messages from './lang';

export default {
  i18n: messages,
  components: {
    UserCard, RuleGroup
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
    rule_groups() {
      return this.user.rule_groups || [];
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
      this.$store.dispatch('user/readWithRuleGroups', { id })
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
