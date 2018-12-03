export default [
  {
    path: '/trainings/definitions/:id(\\d+)',
    component: () => import(/* webpackChunkName: "trainings_chunck" */
      '@/apps/trainings/DefinitionRead.vue'),
    name: 'trainings.definitions.read',
  },
  {
    path: '/trainings/create',
    component: () => import(/* webpackChunkName: "trainings_admin_chunck" */
      '@/apps/trainings/DefinitionForm.vue'),
    name: 'trainings.definitions.create',
  },
  {
    path: '/trainings/update/:id(\\d+)',
    component: () => import(/* webpackChunkName: "trainings_admin_chunck" */
      '@/apps/trainings/DefinitionForm.vue'),
    name: 'trainings.definitions.update',
  },
  {
    path: '/trainings/definitions',
    component: () => import(/* webpackChunkName: "trainings_chunck" */
      '@/apps/trainings/DefinitionBrowse.vue'),
    name: 'trainings.definitions.browse',
  },
];
