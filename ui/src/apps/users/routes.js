import App from './App.vue';

import abilitiesRouter from './abilities';

const UserRead = () => import(
  /* webpackChunkName: "user_chunck" */
  '@/apps/users/UserRead.vue'
);
const UserHeader = () => import(
  /* webpackChunkName: "user_chunck" */
  '@/apps/users/TheUserHeader.vue'
);
const UsersHeader = () => import(
  /* webpackChunkName: "user_chunck" */
  '@/apps/users/TheUsersHeader.vue'
);
const UserBrowse = () => import(
  /* webpackChunkName: "user_chunck" */
  '@/apps/users/UserBrowse.vue'
);

const UserInviteHeader = () => import(
  /* webpackChunkName: "user_chunck" */
  '@/apps/users/TheUserInviteHeader.vue'
);

const UserInvite = () => import(
  /* webpackChunkName: "user_admin" */
  '@/apps/users/UserInvite.vue'
);

const UserRegisterWithInviteHeader = () => import(
  /* webpackChunkName: "user_chunck" */
  '@/apps/users/TheUserRegisterWithInviteHeader.vue'
);
const UserRegisterWithInvite = () => import(
  /* webpackChunkName: "user_admin" */
  '@/apps/users/UserRegisterWithInvite.vue'
);

import UserStore from '@/stores/user';
import InvitationStore from '@/stores/user/invitations';
// import NewsStore from '@/stores/news';
import PageStore from '@/stores/pages';

import makeStore from '@/js/makeVuex';
var store = makeStore();

var routes = [
  {
    path: '/users',
    beforeEnter(to, from, next) {
      store.setModule(['user'], UserStore);
      next();
    },
    component: App,
    children: [
      {
        path: 'invite',
        beforeEnter(to, from, next) {
          store.setModule(['user', 'invitation'], InvitationStore);
          next();
        },
        components: {
          hero: UserInviteHeader,
          default: UserInvite
        },
        name: 'users.invite',
      },
      {
        path: 'invite/:token',
        beforeEnter(to, from, next) {
          store.setModule(['user', 'invitation'], InvitationStore);
          next();
        },
        components: {
          hero: UserRegisterWithInviteHeader,
          default: UserRegisterWithInvite
        },
        name: 'users.register.invite',
      },
      {
        path: ':id(\\d+)',
        beforeEnter(to, from, next) {
//          store.setModule(['news'], NewsStore);
          store.setModule(['page'], PageStore);
          next();
        },
        components: {
          hero: UserHeader,
          default: UserRead
        },
        name: 'users.read',
      },
      {
        path: '',
        components: {
          hero: UsersHeader,
          default: UserBrowse
        },
        name: 'users.browse',
      },
    ]
  },
];

routes = routes.concat(abilitiesRouter);

export default routes;
