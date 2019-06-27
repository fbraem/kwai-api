import App from '@/site/App.vue';

const UserAbilitiesHeader = () =>
  import(/* webpackChunkName: "user_admin_chunck" */
    '@/apps/users/TheUserAbilitiesHeader.vue');
const UserAbilities = () =>
  import(/* webpackChunkName: "user_admin_chunck" */
    '@/apps/users/UserAbilities.vue');

const AbilitiesHeader = () =>
  import(/* webpackChunkName: "user_admin_chunck" */
    '@/apps/users/TheAbilitiesHeader.vue');
const AbilityBrowse = () =>
  import(/* webpackChunkName: "user_admin_chunck" */
    '@/apps/users/AbilityBrowse.vue');

const AbilityFormHeader = () =>
  import(/* webpackChunkName: "user_admin_chunck" */
    '@/apps/users/TheAbilityFormHeader.vue');
const AbilityForm = () =>
  import(/* webpackChunkName: "user_admin_chunck" */
    '@/apps/users/AbilityForm.vue');

import UserStore from '@/stores/user';
import AbilityStore from '@/stores/user/abilities';

import makeStore from '@/js/makeVuex';
var store = makeStore();

export default [
  {
    path: '/users/:id/abilities',
    component: App,
    beforeEnter(to, from, next) {
      store.setModule(['user'], UserStore);
      store.setModule(['user', 'ability'], AbilityStore);
      next();
    },
    children: [
      {
        path: '',
        components: {
          header: UserAbilitiesHeader,
          main: UserAbilities
        },
        name: 'users.abilities',
      },
    ]
  },
  {
    path: '/users/abilities',
    component: App,
    beforeEnter(to, from, next) {
      store.setModule(['user'], UserStore);
      store.setModule(['user', 'rule'], AbilityStore);
      next();
    },
    children: [
      {
        path: 'create',
        components: {
          header: AbilityFormHeader,
          main: AbilityForm
        },
        props: {
          header: {
            creating: true
          }
        },
        name: 'users.abilities.create'
      },
      {
        path: 'update/:id(\\d+)',
        components: {
          header: AbilityFormHeader,
          main: AbilityForm
        },
        props: {
          header: {
            creating: false
          }
        },
        name: 'users.abilities.update'
      },
      {
        path: '',
        components: {
          header: AbilitiesHeader,
          main: AbilityBrowse
        },
        name: 'users.abilities.browse',
      },
    ]
  },
];
