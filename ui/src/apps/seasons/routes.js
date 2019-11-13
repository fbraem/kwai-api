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
const SeasonsHeader = () => import(
  /* webpackChunkName: "seasons_chunck" */
  '@/apps/seasons/TheSeasonsHeader.vue'
);
const SeasonBrowse = () => import(
  /* webpackChunkName: "seasons_chunck" */
  '@/apps/seasons/SeasonBrowse.vue'
);

import SeasonStore from '@/stores/seasons';

import makeStore from '@/js/makeVuex';
var store = makeStore();

export default [
  {
    path: '/seasons',
    beforeEnter(to, from, next) {
      store.setModule(['season'], SeasonStore);
      next();
    },
    component: App,
    children: [
      {
        path: ':id(\\d+)',
        components: {
          hero: SeasonHeader,
          default: SeasonRead
        },
        name: 'seasons.read',
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
