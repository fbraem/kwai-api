import App from '@/site/App.vue';

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

const TeamStore = () => import(
  /* webpackChunkName: "teams_chunck" */
  '@/stores/teams'
);

const TeamTypeStore = () => import(
  /* webpackChunkName: "teams_admin_chunck" */
  '@/stores/team_types'
);
const SeasonStore = () => import(
  /* webpackChunkName: "teams_admin_chunck" */
  '@/stores/seasons'
);

import makeStore from '@/js/makeVuex';
var store = makeStore();

export default [
  {
    path: '/teams',
    component: App,
    async beforeEnter(to, from, next) {
      if (!to.meta.called) {
        to.meta.called = true;
        await store.setModule(['team'], TeamStore);
      }
      next();
    },
    children: [
      {
        path: ':id(\\d+)',
        components: {
          header: TeamHeader,
          main: TeamRead
        },
        name: 'teams.read',
      },
      {
        path: 'create',
        async beforeEnter(to, from, next) {
          if (!to.meta.called) {
            to.meta.called = true;
            await store.setModule(['teamType'], TeamTypeStore);
            await store.setModule(['season'], SeasonStore);
          }
          next();
        },
        components: {
          header: TeamFormHeader,
          main: TeamForm
        },
        props: {
          header: {
            creating: true
          }
        },
        name: 'teams.create',
      },
      {
        path: 'update/:id(\\d+)',
        async beforeEnter(to, from, next) {
          if (!to.meta.called) {
            to.meta.called = true;
            await store.setModule(['teamType'], TeamTypeStore);
            await store.setModule(['season'], SeasonStore);
          }
          next();
        },
        components: {
          header: TeamFormHeader,
          main: TeamForm
        },
        props: {
          header: {
            creating: false
          }
        },
        name: 'teams.update',
      },
      {
        path: '',
        components: {
          header: TeamsHeader,
          main: TeamBrowse
        },
        name: 'teams.browse',
      },
    ]
  },
];
