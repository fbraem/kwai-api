<template>
  <div>
    <table class="uk-table uk-table-divider">
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
</template>

<script>
import RuleGroup from './TheUserRuleGroup';

import messages from './lang';

export default {
  components: {
    RuleGroup
  },
  i18n: messages,
  computed: {
    rule_groups() {
      return this.$store.state.user.rule.rule_groups || [];
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
      this.$store.dispatch('user/rule/browse')
        .catch((error) => {
          console.log(error);
        });
    },
  }
};
</script>
