import App from './App.vue';

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

export default [
  {
    path: '/trainings/definitions',
    component: App,
    children: [
      {
        path: ':id(\\d+)',
        components: {
          hero: DefinitionHeader,
          default: DefinitionRead
        },
        name: 'trainings.definitions.read',
      },
      {
        path: 'create',
        components: {
          hero: DefinitionFormHeader,
          default: DefinitionForm
        },
        props: {
          hero: {
            creating: true
          }
        },
        name: 'trainings.definitions.create',
      },
      {
        path: 'update/:id(\\d+)',
        components: {
          hero: DefinitionFormHeader,
          default: DefinitionForm
        },
        props: {
          hero: {
            creating: false
          }
        },
        name: 'trainings.definitions.update',
      },
      {
        path: '',
        components: {
          hero: DefinitionsHeader,
          default: DefinitionBrowse
        },
        name: 'trainings.definitions.browse',
      },
    ]
  },
];
