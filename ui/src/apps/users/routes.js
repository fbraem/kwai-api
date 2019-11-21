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

var routes = [
  {
    path: '/users',
    component: App,
    children: [
      {
        path: 'invite',
        components: {
          hero: UserInviteHeader,
          default: UserInvite
        },
        name: 'users.invite',
      },
      {
        path: 'invite/:token',
        components: {
          hero: UserRegisterWithInviteHeader,
          default: UserRegisterWithInvite
        },
        name: 'users.register.invite',
      },
      {
        path: ':id(\\d+)',
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
