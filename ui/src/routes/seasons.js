import App from '@/site/App.vue';

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
const SeasonsHeader = () => import(
  /* webpackChunkName: "seasons_chunck" */
  '@/apps/seasons/TheSeasonsHeader.vue'
);
const SeasonBrowse = () => import(
  /* webpackChunkName: "seasons_chunck" */
  '@/apps/seasons/SeasonBrowse.vue'
);

const SeasonStore = () => import(
  /* webpackChunkName: "seasons_chunck" */
  '@/stores/seasons'
);

export default [
  {
    path: '/seasons',
    meta: {
      stores: [
        {
          ns: [ 'season' ],
          create: SeasonStore
        },
      ]
    },
    component: App,
    children: [
      {
        path: ':id(\\d+)',
        components: {
          header: SeasonHeader,
          main: SeasonRead
        },
        name: 'seasons.read',
      },
      {
        path: 'create',
        components: {
          header: SeasonFormHeader,
          main: SeasonForm
        },
        props: {
          header: {
            creating: true
          }
        },
        name: 'seasons.create',
      },
      {
        path: 'update/:id(\\d+)',
        components: {
          header: SeasonFormHeader,
          main: SeasonForm
        },
        props: {
          header: {
            creating: false
          }
        },
        name: 'seasons.update',
      },
      {
        path: '',
        components: {
          header: SeasonsHeader,
          main: SeasonBrowse
        },
        name: 'seasons.browse',
      },
    ]
  },
];
