export default [
  // {
  //   path: '/events/create',
  //   component: () => import(/* webpackChunkName: "events_admin_chunck" */
  //     '@/apps/events/EventForm.vue'),
  //   name: 'events.create',
  // },
  // {
  //   path: '/events/update/:id(\\d+)',
  //   component: () => import(/* webpackChunkName: "events_admin_chunck" */
  //     '@/apps/events/EventForm.vue'),
  //   name: 'events.update',
  // },
  {
    path: '/events/:year(\\d+)/:month(\\d+)',
    component: () => import(/* webpackChunkName: "trainings_chunck" */
      '@/apps/events/EventBrowse.vue'),
    name: 'events.browse',
    props(route) {
      var result = {};
      if (route.params.year) result.year = Number(route.params.year);
      if (route.params.month) result.month = Number(route.params.month);
      return result;
    }
  },
  {
    path: '/events/:id(\\d+)',
    component: () => import(/* webpackChunkName: "events_chunck" */
      '@/apps/events/EventRead.vue'),
    name: 'events.read'
  },
  {
    path: '/events',
    component: () => import(/* webpackChunkName: "events_chunck" */
      '@/apps/events/EventBrowse.vue'),
    name: 'events.home'
  },
];
