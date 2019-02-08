export default [
  {
    path: '/trainings/definitions/:id(\\d+)',
    component: () => import(/* webpackChunkName: "trainings_admin_chunck" */
      '@/apps/trainings/DefinitionRead.vue'),
    name: 'trainings.definitions.read',
  },
  {
    path: '/trainings/definitions/create',
    component: () => import(/* webpackChunkName: "trainings_admin_chunck" */
      '@/apps/trainings/DefinitionForm.vue'),
    name: 'trainings.definitions.create',
  },
  {
    path: '/trainings/definitions/update/:id(\\d+)',
    component: () => import(/* webpackChunkName: "trainings_admin_chunck" */
      '@/apps/trainings/DefinitionForm.vue'),
    name: 'trainings.definitions.update',
  },
  {
    path: '/trainings/definitions',
    component: () => import(/* webpackChunkName: "trainings_admin_chunck" */
      '@/apps/trainings/DefinitionBrowse.vue'),
    name: 'trainings.definitions.browse',
  },
  {
    path: '/trainings/coaches/:id(\\d+)',
    component: () => import(/* webpackChunkName: "trainings_admin_chunck" */
      '@/apps/trainings/CoachRead.vue'),
    name: 'trainings.coaches.read',
  },
  {
    path: '/trainings/coaches',
    component: () => import(/* webpackChunkName: "trainings_admin_chunck" */
      '@/apps/trainings/CoachBrowse.vue'),
    name: 'trainings.coaches.browse',
  },
  {
    path: '/trainings/coaches/create',
    component: () => import(/* webpackChunkName: "trainings_admin_chunck" */
      '@/apps/trainings/CoachForm.vue'),
    name: 'trainings.coaches.create',
  },
  {
    path: '/trainings/coaches/update/:id(\\d+)',
    component: () => import(/* webpackChunkName: "trainings_admin_chunck" */
      '@/apps/trainings/CoachForm.vue'),
    name: 'trainings.coaches.update',
  },
  {
    path: '/trainings/:year(\\d+)/:month(\\d+)',
    component: () => import(/* webpackChunkName: "trainings_chunck" */
      '@/apps/trainings/TrainingBrowse.vue'),
    name: 'trainings.browse'
  },
  {
    path: '/trainings/:id(\\d+)',
    component: () => import(/* webpackChunkName: "trainings_chunck" */
      '@/apps/trainings/TrainingRead.vue'),
    name: 'trainings.read'
  },
  {
    path: '/trainings/create',
    component: () => import(/* webpackChunkName: "trainings_admin_chunck" */
      '@/apps/trainings/TrainingForm.vue'),
    name: 'trainings.create',
  },
  {
    path: '/trainings/update/:id(\\d+)',
    component: () => import(/* webpackChunkName: "trainings_admin_chunck" */
      '@/apps/trainings/TrainingForm.vue'),
    name: 'trainings.update',
  },
  {
    path: '/trainings',
    component: () => import(/* webpackChunkName: "trainings_chunck" */
      '@/apps/trainings/TrainingBrowse.vue'),
    name: 'trainings.home'
  },
];
