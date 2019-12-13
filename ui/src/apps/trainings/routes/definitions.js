import App from '../App.vue';

const DefinitionBrowse = () =>
  import(/* webpackChunkName: "trainings_admin_chunck" */
    '@/apps/trainings/DefinitionBrowse.vue');

const DefinitionForm = () =>
  import(/* webpackChunkName: "trainings_admin_chunck" */
    '@/apps/trainings/DefinitionForm.vue');

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
        component: DefinitionRead,
        name: 'trainings.definitions.read',
      },
      {
        path: 'create',
        component: DefinitionForm,
        props: {
          creating: true
        },
        name: 'trainings.definitions.create',
      },
      {
        path: 'update/:id(\\d+)',
        component: DefinitionForm,
        props: {
          creating: false
        },
        name: 'trainings.definitions.update',
      },
      {
        path: '',
        component: DefinitionBrowse,
        name: 'trainings.definitions.browse',
      },
    ]
  },
];
