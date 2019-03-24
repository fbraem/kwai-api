import App from '@/site/App.vue';

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

const UserStore = () => import(
  /* webpackChunkName: "user_chunck" */
  '@/stores/users'
);
const NewsStore = () => import(
  /* webpackChunkName: "news_chunck" */
  '@/stores/news'
);
const PageStore = () => import(
  /* webpackChunkName: "pages_chunck" */
  '@/stores/pages'
);

export default [
  {
    path: '/users',
    meta: {
      stores: [
        {
          ns: [ 'user' ],
          create: UserStore
        },
      ]
    },
    component: App,
    children: [
      {
        path: 'invite',
        components: {
          header: UserInviteHeader,
          main: UserInvite
        },
        name: 'users.invite',
      },
      {
        path: 'invite/:token',
        components: {
          header: UserRegisterWithInviteHeader,
          main: UserRegisterWithInvite
        },
        name: 'users.register.invite',
      },
      {
        path: ':id',
        meta: {
          stores: [
            {
              ns: [ 'news' ],
              create: NewsStore
            },
            {
              ns: [ 'page' ],
              create: PageStore
            },
          ]
        },
        components: {
          header: UserHeader,
          main: UserRead
        },
        name: 'users.read',
      },
      {
        path: '',
        components: {
          header: UsersHeader,
          main: UserBrowse
        },
        name: 'users.browse',
      },
    ]
  },
];
