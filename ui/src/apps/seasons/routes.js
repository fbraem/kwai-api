import App from './App.vue';

const SeasonHeader = () => import(
  /* webpackChunkName: "seasons_chunck" */
  '@/apps/seasons/TheSeasonHeader.vue'
);
const SeasonRead = () => import(
  /* webpackChunkName: "seasons_chunck" */
  '@/apps/seasons/SeasonRead.vue'
);
const SeasonFormHeader = () => import(
  /* webpackChunkName: "seasons_admin" */
  '@/apps/seasons/TheSeasonFormHeader.vue'
);
const SeasonForm = () => import(
  /* webpackChunkName: "seasons_admin" */
  '@/apps/seasons/SeasonForm.vue'
);
const SeasonTeams = () => import(
  /* webpackChunkName: "seasons_admin" */
  '@/apps/seasons/SeasonTeams.vue'
);
const SeasonDefinitions = () => import(
  /* webpackChunkName: "seasons_admin" */
  '@/apps/seasons/SeasonDefinitions.vue'
);
const SeasonTrainings = () => import(
  /* webpackChunkName: "seasons_admin" */
  '@/apps/seasons/SeasonTrainings.vue'
);
const SeasonDetail = () => import(
  /* webpackChunkName: "seasons_admin" */
  '@/apps/seasons/SeasonDetail.vue'
);
const SeasonsHeader = () => import(
  /* webpackChunkName: "seasons_chunck" */
  '@/apps/seasons/TheSeasonsHeader.vue'
);
const SeasonBrowse = () => import(
  /* webpackChunkName: "seasons_chunck" */
  '@/apps/seasons/SeasonBrowse.vue'
);

export default [
  {
    path: '/seasons',
    component: App,
    children: [
      {
        path: ':id(\\d+)',
        components: {
          hero: SeasonHeader,
          default: SeasonRead
        },
        children: [
          {
            path: 'teams',
            components: {
              season_information: SeasonTeams
            },
            name: 'seasons.teams'
          },
          {
            path: 'definitions',
            components: {
              season_information: SeasonDefinitions
            },
            name: 'seasons.definitions'
          },
          {
            path: 'trainings',
            components: {
              season_information: SeasonTrainings
            },
            name: 'seasons.trainings'
          },
          {
            path: '',
            components: {
              season_information: SeasonDetail
            },
            name: 'seasons.read',
          },
        ]
      },
      {
        path: 'create',
        components: {
          hero: SeasonFormHeader,
          default: SeasonForm
        },
        props: {
          hero: {
            creating: true
          }
        },
        name: 'seasons.create',
      },
      {
        path: 'update/:id(\\d+)',
        components: {
          hero: SeasonFormHeader,
          default: SeasonForm
        },
        props: {
          hero: {
            creating: false
          }
        },
        name: 'seasons.update',
      },
      {
        path: '',
        components: {
          hero: SeasonsHeader,
          default: SeasonBrowse
        },
        name: 'seasons.browse',
      },
    ]
  },
];
