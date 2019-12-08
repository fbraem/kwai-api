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

const TeamCategoryRead = () => import(
  /* webpackChunkName: "teams_admin_chunck" */
  './TeamCategoryRead.vue'
);

const TeamCategoryHeader = () => import(
  /* webpackChunkName: "teams_admin_chunck" */
  './TheTeamCategoryHeader.vue'
);
const TeamCategoryBrowse = () => import(
  /* webpackChunkName: "teams_admin_chunck" */
  './TeamCategoryBrowse.vue'
);
const TeamCategoriesHeader = () => import(
  /* webpackChunkName: "teams_admin_chunck" */
  './TheTeamCategoriesHeader.vue'
);
const TeamCategoryForm = () => import(
  /* webpackChunkName: "teams_admin_chunck" */
  './TeamCategoryForm.vue'
);
const TeamCategoryFormHeader = () => import(
  /* webpackChunkName: "teams_admin_chunck" */
  './TheTeamCategoryFormHeader.vue'
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
        path: 'categories/:id(\\d+)',
        components: {
          hero: TeamCategoryHeader,
          default: TeamCategoryRead
        },
        name: 'team_categories.read',
      },
      {
        path: 'categories/create',
        components: {
          hero: TeamCategoryFormHeader,
          default: TeamCategoryForm
        },
        props: {
          hero: {
            creating: true
          }
        },
        name: 'team_categories.create',
      },
      {
        path: 'categories/update/:id(\\d+)',
        components: {
          hero: TeamCategoryFormHeader,
          default: TeamCategoryForm
        },
        props: {
          hero: {
            creating: false
          }
        },
        name: 'team_categories.update',
      },
      {
        path: 'categories',
        components: {
          hero: TeamCategoriesHeader,
          default: TeamCategoryBrowse
        },
        name: 'team_types.browse',
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
