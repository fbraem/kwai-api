export default [
  {
    path: '/trainings/definitions/:id(\\d+)',
    component: () => import(/* webpackChunkName: "trainings_chunck" */
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
    component: () => import(/* webpackChunkName: "trainings_chunck" */
      '@/apps/trainings/DefinitionBrowse.vue'),
    name: 'trainings.definitions.browse',
  },
  {
    path: '/trainings/coaches/:id(\\d+)',
    component: () => import(/* webpackChunkName: "trainings_chunck" */
      '@/apps/trainings/CoachRead.vue'),
    name: 'trainings.coaches.read',
  },
  {
    path: '/trainings/coaches',
    component: () => import(/* webpackChunkName: "trainings_chunck" */
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
    path: '/trainings/events/generate',
    component: () => import(/* webpackChunkName: "trainings_admin_chunck" */
      '@/apps/trainings/EventGenerate.vue'),
    name: 'trainings.events.generate',
  },
  {
    path: '/trainings/events/:year(\\d+)/:month(\\d+)',
    component: () => import(/* webpackChunkName: "trainings_chunck" */
      '@/apps/trainings/EventBrowse.vue'),
    name: 'trainings.events.browse',
    props(route) {
      var result = {};
      if (route.params.year) result.year = Number(route.params.year);
      if (route.params.month) result.month = Number(route.params.month);
      return result;
    }
  },
  {
    path: '/trainings/events/:id(\\d+)',
    component: () => import(/* webpackChunkName: "trainings_chunck" */
      '@/apps/trainings/EventRead.vue'),
    name: 'trainings.events.read'
  },
  {
    path: '/trainings/events',
    component: () => import(/* webpackChunkName: "trainings_chunck" */
      '@/apps/trainings/EventBrowse.vue'),
    name: 'trainings.events.home'
  },
];
