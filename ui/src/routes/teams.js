export default [
    {
        path : '/teams/:id(\\d+)',
        component : () => import(/* webpackChunkName: "teams_chunck" */ '@/apps/teams/TeamRead.vue'),
        name : 'teams.read'
    },
    {
        path : '/teams/create',
        component : () => import(/* webpackChunkName: "teams_admin" */ '@/apps/teams/TeamForm.vue'),
        name : 'teams.create'
    },
    {
        path : '/teams/update/:id(\\d+)',
        component : () => import(/* webpackChunkName: "teams_admin" */ '@/apps/teams/TeamForm.vue'),
        name : 'teams.update'
    },
    {
        path : '/teams',
        component : () => import(/* webpackChunkName: "teams_chunck" */ '@/apps/teams/TeamBrowse.vue'),
        name : 'teams.browse'
    }
];
