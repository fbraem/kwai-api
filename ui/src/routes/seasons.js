import App from '@/site/App.vue';

const SeasonHeader = () => import(
  /* webpackChunkName: "seasons_chunck" */ '@/apps/seasons/SeasonHeader.vue'
);
const SeasonRead = () => import(
  /* webpackChunkName: "seasons_chunck" */ '@/apps/seasons/SeasonRead.vue'
);
const SeasonFormHeader = () => import(
  /* webpackChunkName: "seasons_admin" */ '@/apps/seasons/SeasonFormHeader.vue'
);
const SeasonForm = () => import(
  /* webpackChunkName: "seasons_admin" */ '@/apps/seasons/SeasonForm.vue'
);
const SeasonsHeader = () => import(
  /* webpackChunkName: "seasons_chunck" */ '@/apps/seasons/SeasonsHeader.vue'
);
const SeasonBrowse = () => import(
  /* webpackChunkName: "seasons_chunck" */ '@/apps/seasons/SeasonBrowse.vue'
);

export default [
  {
    path: '/seasons',
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
