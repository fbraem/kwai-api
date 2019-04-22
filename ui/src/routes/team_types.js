import App from '@/site/App.vue';

const TeamTypeRead = () => import(
  /* webpackChunkName: "team_types_chunck" */
  '@/apps/team_types/TeamTypeRead.vue'
);
const TeamTypeHeader = () => import(
  /* webpackChunkName: "team_types_chunck" */
  '@/apps/team_types/TheTeamTypeHeader.vue'
);
const TeamTypeBrowse = () => import(
  /* webpackChunkName: "team_types_chunck" */
  '@/apps/team_types/TeamTypeBrowse.vue'
);
const TeamTypesHeader = () => import(
  /* webpackChunkName: "team_types_chunck" */
  '@/apps/team_types/TheTeamTypesHeader.vue'
);
const TeamTypeForm = () => import(
  /* webpackChunkName: "team_types_chunck" */
  '@/apps/team_types/TeamTypeForm.vue'
);
const TeamTypeFormHeader = () => import(
  /* webpackChunkName: "team_types_chunck" */
  '@/apps/team_types/TheTeamTypeFormHeader.vue'
);

import TeamTypeStore from '@/stores/team_types';

import makeStore from '@/js/makeVuex';
var store = makeStore();

export default [
  {
    path: '/team_types',
    component: App,
    beforeEnter(to, from, next) {
      store.setModule(['teamType'], TeamTypeStore);
      next();
    },
    children: [
      {
        path: ':id(\\d+)',
        components: {
          header: TeamTypeHeader,
          main: TeamTypeRead
        },
        name: 'team_types.read',
      },
      {
        path: 'create',
        components: {
          header: TeamTypeFormHeader,
          main: TeamTypeForm
        },
        props: {
          header: {
            creating: true
          }
        },
        name: 'team_types.create',
      },
      {
        path: 'update/:id(\\d+)',
        components: {
          header: TeamTypeFormHeader,
          main: TeamTypeForm
        },
        props: {
          header: {
            creating: false
          }
        },
        name: 'team_types.update',
      },
      {
        path: '',
        components: {
          header: TeamTypesHeader,
          main: TeamTypeBrowse
        },
        name: 'team_types.browse',
      },
    ]
  },
];
