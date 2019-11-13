import App from './App.vue';

const TeamRead = () => import(
  /* webpackChunkName: "teams_chunck" */
  '@/apps/teams/TeamRead.vue'
);
const TeamHeader = () => import(
  /* webpackChunkName: "teams_chunck" */
  '@/apps/teams/TheTeamHeader.vue'
);
const TeamBrowse = () => import(
  /* webpackChunkName: "teams_chunck" */
  '@/apps/teams/TeamBrowse.vue'
);
const TeamsHeader = () => import(
  /* webpackChunkName: "teams_chunck" */
  '@/apps/teams/TheTeamsHeader.vue'
);
const TeamForm = () => import(
  /* webpackChunkName: "teams_admin_chunck" */
  '@/apps/teams/TeamForm.vue'
);
const TeamFormHeader = () => import(
  /* webpackChunkName: "teams_admin_chunck" */
  '@/apps/teams/TheTeamFormHeader.vue'
);

import TeamStore from '@/stores/teams';
import TeamTypeStore from '@/stores/team_types';
import SeasonStore from '@/stores/seasons';

import makeStore from '@/js/makeVuex';
var store = makeStore();

export default [
  {
    path: '/teams',
    component: App,
    beforeEnter(to, from, next) {
      store.setModule(['team'], TeamStore);
      next();
    },
    children: [
      {
        path: ':id(\\d+)',
        components: {
          hero: TeamHeader,
          default: TeamRead
        },
        name: 'teams.read',
      },
      {
        path: 'create',
        beforeEnter(to, from, next) {
          store.setModule(['teamType'], TeamTypeStore);
          store.setModule(['season'], SeasonStore);
          next();
        },
        components: {
          hero: TeamFormHeader,
          default: TeamForm
        },
        props: {
          hero: {
            creating: true
          }
        },
        name: 'teams.create',
      },
      {
        path: 'update/:id(\\d+)',
        beforeEnter(to, from, next) {
          store.setModule(['teamType'], TeamTypeStore);
          store.setModule(['season'], SeasonStore);
          next();
        },
        components: {
          hero: TeamFormHeader,
          default: TeamForm
        },
        props: {
          hero: {
            creating: false
          }
        },
        name: 'teams.update',
      },
      {
        path: '',
        components: {
          hero: TeamsHeader,
          default: TeamBrowse
        },
        name: 'teams.browse',
      },
    ]
  },
];
