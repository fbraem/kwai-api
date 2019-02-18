import App from '@/site/App.vue';

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

export default [
  {
    path: '/trainings/definitions',
    component: App,
    meta: {
      stores: [
        {
          ns: [ 'training' ],
          create: TrainingStore
        },
        {
          ns: [ 'training', 'definition' ],
          create: DefinitionStore
        },
      ]
    },
    children: [
      {
        path: ':id(\\d+)',
        meta: {
          stores: [
            {
              ns: [ 'training', 'coach' ],
              create: CoachStore
            },
          ]
        },
        components: {
          header: DefinitionHeader,
          main: DefinitionRead
        },
        name: 'trainings.definitions.read',
      },
      {
        path: 'create',
        meta: {
          stores: [
            {
              ns: [ 'season' ],
              create: SeasonStore
            },
            {
              ns: [ 'team' ],
              create: TeamStore
            },
          ]
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
        meta: {
          stores: [
            {
              ns: [ 'season' ],
              create: SeasonStore
            },
            {
              ns: [ 'team' ],
              create: TeamStore
            },
          ]
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
