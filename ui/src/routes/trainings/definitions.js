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

import TrainingStore from '@/stores/training';
import DefinitionStore from '@/stores/training/definitions';
import CoachStore from '@/stores/training/coaches';
import SeasonStore from '@/stores/seasons';
import TeamStore from '@/stores/teams';

import makeStore from '@/js/makeVuex';
var store = makeStore();

export default [
  {
    path: '/trainings/definitions',
    component: App,
    beforeEnter(to, from, next) {
      store.setModule(['training'], TrainingStore);
      store.setModule(['training', 'definition'], DefinitionStore);
      next();
    },
    children: [
      {
        path: ':id(\\d+)',
        beforeEnter(to, from, next) {
          store.setModule(['training', 'coach'], CoachStore);
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
        beforeEnter(to, from, next) {
          store.setModule(['season'], SeasonStore);
          store.setModule(['team'], TeamStore);
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
        beforeEnter(to, from, next) {
          store.setModule(['season'], SeasonStore);
          store.setModule(['team'], TeamStore);
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
