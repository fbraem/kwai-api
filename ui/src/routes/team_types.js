export default [
  {
    path: '/team_types/:id(\\d+)',
    component: () => import(
      /* webpackChunkName: "team_types_chunck" */ '@/apps/team_types/TeamTypeRead.vue'
    ),
    name: 'team_types.read',
  },
  {
    path: '/team_types/create',
    component: () => import(
      /* webpackChunkName: "team_types_admin" */ '@/apps/team_types/TeamTypeForm.vue'
    ),
    name: 'team_types.create',
  },
  {
    path: '/team_types/update/:id(\\d+)',
    component: () => import(
      /* webpackChunkName: "team_types_admin" */ '@/apps/team_types/TeamTypeForm.vue'
    ),
    name: 'team_types.update',
  },
  {
    path: '/team_types',
    component: () => import(
      /* webpackChunkName: "team_types_chunck" */ '@/apps/team_types/TeamTypeBrowse.vue'
    ),
    name: 'team_types.browse',
  },
];
