import App from '@/site/App.vue';

const UserRulesHeader = () =>
  import(/* webpackChunkName: "user_admin_chunck" */
    '@/apps/users/TheUserRulesHeader.vue');
const UserRules = () =>
  import(/* webpackChunkName: "user_admin_chunck" */
    '@/apps/users/UserRules.vue');

const RuleGroupsHeader = () =>
  import(/* webpackChunkName: "user_admin_chunck" */
    '@/apps/users/TheRuleGroupsHeader.vue');
const RuleGroupBrowse = () =>
  import(/* webpackChunkName: "user_admin_chunck" */
    '@/apps/users/RuleGroupBrowse.vue');

const RuleGroupFormHeader = () =>
  import(/* webpackChunkName: "user_admin_chunck" */
    '@/apps/users/TheRuleGroupFormHeader.vue');
const RuleGroupForm = () =>
  import(/* webpackChunkName: "user_admin_chunck" */
    '@/apps/users/RuleGroupForm.vue');

import UserStore from '@/stores/user';
import RuleStore from '@/stores/user/rules';

import makeStore from '@/js/makeVuex';
var store = makeStore();

export default [
  {
    path: '/users/:id/rules',
    component: App,
    beforeEnter(to, from, next) {
      store.setModule(['user'], UserStore);
      store.setModule(['user', 'rule'], RuleStore);
      next();
    },
    children: [
      {
        path: '',
        components: {
          header: UserRulesHeader,
          main: UserRules
        },
        name: 'users.rules',
      },
    ]
  },
  {
    path: '/users/rule_groups',
    component: App,
    beforeEnter(to, from, next) {
      store.setModule(['user'], UserStore);
      store.setModule(['user', 'rule'], RuleStore);
      next();
    },
    children: [
      {
        path: 'create',
        components: {
          header: RuleGroupFormHeader,
          main: RuleGroupForm
        },
        props: {
          header: {
            creating: true
          }
        },
        name: 'users.rule_groups.create'
      },
      {
        path: 'update/:id(\\d+)',
        components: {
          header: RuleGroupFormHeader,
          main: RuleGroupForm
        },
        props: {
          header: {
            creating: false
          }
        },
        name: 'users.rule_groups.update'
      },
      {
        path: '',
        components: {
          header: RuleGroupsHeader,
          main: RuleGroupBrowse
        },
        name: 'users.rule_groups.browse',
      },
    ]
  },
];
