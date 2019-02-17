const DefinitionsHeader = () =>
  import(/* webpackChunkName: "trainings_admin_chunck" */
    '@/apps/trainings/DefinitionsHeader.vue');
const DefinitionBrowse = () =>
  import(/* webpackChunkName: "trainings_admin_chunck" */
    '@/apps/trainings/DefinitionBrowse.vue');

const DefinitionFormHeader = () =>
  import(/* webpackChunkName: "trainings_admin_chunck" */
    '@/apps/trainings/DefinitionFormHeader.vue');
const DefinitionForm = () =>
  import(/* webpackChunkName: "trainings_admin_chunck" */
    '@/apps/trainings/DefinitionForm.vue');

const DefinitionHeader = () =>
  import(/* webpackChunkName: "trainings_admin_chunck" */
    '@/apps/trainings/DefinitionHeader.vue');
const DefinitionRead = () =>
  import(/* webpackChunkName: "trainings_admin_chunck" */
    '@/apps/trainings/DefinitionRead.vue');

export default [
  {
    path: 'definitions/:id(\\d+)',
    components: {
      header: DefinitionHeader,
      main: DefinitionRead
    },
    name: 'trainings.definitions.read',
  },
  {
    path: 'definitions/create',
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
    path: 'definitions/update/:id(\\d+)',
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
    path: 'definitions',
    components: {
      header: DefinitionsHeader,
      main: DefinitionBrowse
    },
    name: 'trainings.definitions.browse',
  },
];
