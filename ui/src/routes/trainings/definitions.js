import App from '@/site/App.vue';

const DefinitionsHeader = () =>
  import(/* webpackChunkName: "trainings_admin_chunck" */
    '@/apps/trainings/TheDefinitionsHeader.vue');
const DefinitionBrowse = () =>
  import(/* webpackChunkName: "trainings_admin_chunck" */
    '@/apps/trainings/DefinitionBrowse.vue');

const DefinitionFormHeader = () =>
  import(/* webpackChunkName: "trainings_admin_chunck" */
    '@/apps/trainings/TheDefinitionFormHeader.vue');
const DefinitionForm = () =>
  import(/* webpackChunkName: "trainings_admin_chunck" */
    '@/apps/trainings/DefinitionForm.vue');

const DefinitionHeader = () =>
  import(/* webpackChunkName: "trainings_admin_chunck" */
    '@/apps/trainings/TheDefinitionHeader.vue');
const DefinitionRead = () =>
  import(/* webpackChunkName: "trainings_admin_chunck" */
    '@/apps/trainings/DefinitionRead.vue');

const TrainingStore = () =>
  import('@/stores/training');
const DefinitionStore = () =>
  import('@/stores/training/definitions');
const CoachStore = () =>
  import('@/stores/training/coaches');
const SeasonStore = () =>
  import('@/stores/seasons');
const TeamStore = () =>
  import('@/stores/teams');

import makeStore from '@/js/makeVuex';
var store = makeStore();

export default [
  {
    path: '/trainings/definitions',
    component: App,
    async beforeEnter(to, from, next) {
      if (!to.meta.called) {
        to.meta.called = true;
        await store.setModule(['training'], TrainingStore);
        await store.setModule(['training', 'definition'], DefinitionStore);
      }
      next();
    },
    children: [
      {
        path: ':id(\\d+)',
        async beforeEnter(to, from, next) {
          await store.setModule(['training', 'coach'], CoachStore);
          next();
        },
        components: {
          header: DefinitionHeader,
          main: DefinitionRead
        },
        name: 'trainings.definitions.read',
      },
      {
        path: 'create',
        async beforeEnter(to, from, next) {
          await store.setModule(['season'], SeasonStore);
          await store.setModule(['team'], TeamStore);
          next();
        },
        components: {
          header: DefinitionFormHeader,
          main: DefinitionForm
        },
        props: {
          header: {
            creating: true
          }
        },
        name: 'trainings.definitions.create',
      },
      {
        path: 'update/:id(\\d+)',
        async beforeEnter(to, from, next) {
          await store.setModule(['season'], SeasonStore);
          await store.setModule(['team'], TeamStore);
          next();
        },
        components: {
          header: DefinitionFormHeader,
          main: DefinitionForm
        },
        props: {
          header: {
            creating: false
          }
        },
        name: 'trainings.definitions.update',
      },
      {
        path: '',
        components: {
          header: DefinitionsHeader,
          main: DefinitionBrowse
        },
        name: 'trainings.definitions.browse',
      },
    ]
  },
];
