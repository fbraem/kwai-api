import App from '@/site/App.vue';

const TeamRead = () => import(
  /* webpackChunkName: "teams_chunck" */ '@/apps/teams/TeamRead.vue'
);
const TeamHeader = () => import(
  /* webpackChunkName: "teams_chunck" */ '@/apps/teams/TeamHeader.vue'
);
const TeamBrowse = () => import(
  /* webpackChunkName: "teams_chunck" */ '@/apps/teams/TeamBrowse.vue'
);
const TeamsHeader = () => import(
  /* webpackChunkName: "teams_chunck" */ '@/apps/teams/TeamsHeader.vue'
);
const TeamForm = () => import(
  /* webpackChunkName: "teams_chunck" */ '@/apps/teams/TeamForm.vue'
);
const TeamFormHeader = () => import(
  /* webpackChunkName: "teams_chunck" */ '@/apps/teams/TeamFormHeader.vue'
);

export default [
  {
    path: '/teams',
    component: App,
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
