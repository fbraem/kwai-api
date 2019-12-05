import App from './App.vue';

const TeamRead = () => import(
  /* webpackChunkName: "teams_chunck" */
  './TeamRead.vue'
);
const TeamHeader = () => import(
  /* webpackChunkName: "teams_chunck" */
  './TheTeamHeader.vue'
);
const TeamDetails = () => import(
  /* webpackChunkName: "teams_chunck" */
  './TeamDetails.vue'
);
const TeamTrainings = () => import(
  /* webpackChunkName: "teams_chunck" */
  './NotImplemented.vue'
);
const TeamMembers = () => import(
  /* webpackChunkName: "teams_chunck" */
  './TeamMembers.vue'
);
const TeamTournaments = () => import(
  /* webpackChunkName: "teams_chunck" */
  './NotImplemented.vue'
);

const TeamBrowse = () => import(
  /* webpackChunkName: "teams_chunck" */
  './TeamBrowse.vue'
);
const TeamsHeader = () => import(
  /* webpackChunkName: "teams_chunck" */
  './TheTeamsHeader.vue'
);
const TeamForm = () => import(
  /* webpackChunkName: "teams_admin_chunck" */
  './TeamForm.vue'
);
const TeamFormHeader = () => import(
  /* webpackChunkName: "teams_admin_chunck" */
  './TheTeamFormHeader.vue'
);

const TeamTypeRead = () => import(
  /* webpackChunkName: "teams_admin_chunck" */
  './TeamTypeRead.vue'
);

const TeamTypeHeader = () => import(
  /* webpackChunkName: "teams_admin_chunck" */
  './TheTeamTypeHeader.vue'
);
const TeamTypeBrowse = () => import(
  /* webpackChunkName: "teams_admin_chunck" */
  '@/apps/team_types/TeamTypeBrowse.vue'
);
const TeamTypesHeader = () => import(
  /* webpackChunkName: "teams_admin_chunck" */
  './TheTeamTypesHeader.vue'
);
const TeamTypeForm = () => import(
  /* webpackChunkName: "teams_admin_chunck" */
  './TeamTypeForm.vue'
);
const TeamTypeFormHeader = () => import(
  /* webpackChunkName: "teams_admin_chunck" */
  './TheTeamTypeFormHeader.vue'
);

export default [
  {
    path: '/teams',
    component: App,
    children: [
      {
        path: ':id(\\d+)',
        components: {
          hero: TeamHeader,
          default: TeamRead
        },
        children: [
          {
            path: 'members',
            components: {
              team_information: TeamMembers
            },
            name: 'team.members'
          },
          {
            path: 'season',
            components: {
              team_information: TeamTournaments
            },
            name: 'team.tournaments'
          },
          {
            path: 'trainings',
            components: {
              team_information: TeamTrainings
            },
            name: 'team.trainings'
          },
          {
            path: '',
            components: {
              team_information: TeamDetails
            },
            name: 'teams.read',
          },
        ]
      },
      {
        path: 'create',
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
        path: '/team_types',
        component: App,
        children: [
          {
            path: ':id(\\d+)',
            components: {
              hero: TeamTypeHeader,
              default: TeamTypeRead
            },
            name: 'team_types.read',
          },
          {
            path: 'create',
            components: {
              hero: TeamTypeFormHeader,
              default: TeamTypeForm
            },
            props: {
              hero: {
                creating: true
              }
            },
            name: 'team_types.create',
          },
          {
            path: 'update/:id(\\d+)',
            components: {
              hero: TeamTypeFormHeader,
              default: TeamTypeForm
            },
            props: {
              hero: {
                creating: false
              }
            },
            name: 'team_types.update',
          },
          {
            path: '',
            components: {
              hero: TeamTypesHeader,
              default: TeamTypeBrowse
            },
            name: 'team_types.browse',
          },
        ]
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
