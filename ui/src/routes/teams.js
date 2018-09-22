export default [
    {
        path : '/teams/:id(\\d+)',
        component : () => import(/* webpackChunkName: "teams_chunck" */ '@/apps/teams/app/TeamRead.vue'),
        name : 'teams.read'
    },
    {
        path : '/teams/create',
        component : () => import(/* webpackChunkName: "teams_admin" */ '@/apps/teams/app/TeamForm.vue'),
        name : 'teams.create'
    },
    {
        path : '/teams/update/:id(\\d+)',
        component : () => import(/* webpackChunkName: "teams_admin" */ '@/apps/teams/app/TeamForm.vue'),
        name : 'teams.update'
    },
    {
        path : '/teams',
        component : () => import(/* webpackChunkName: "teams_chunck" */ '@/apps/teams/app/TeamBrowse.vue'),
        name : 'teams.browse'
    },
    {
        path : '/team_types/:id(\\d+)',
        component : () => import(/* webpackChunkName: "teams_chunck" */ '@/apps/teams/app/TeamTypeRead.vue'),
        name : 'teamtypes.read'
    },
    {
        path : '/team_types/create',
        component : () => import(/* webpackChunkName: "teams_admin" */ '@/apps/teams/app/TeamTypeCreate.vue'),
        name : 'teamtypes.create'
    },
    {
        path : '/team_types/update/:id(\\d+)',
        component : () => import(/* webpackChunkName: "teams_admin" */ '@/apps/teams/app/TeamTypeUpdate.vue'),
        name : 'teamtype.update'
    },
    {
        path : 'team_types',
        component : () => import(/* webpackChunkName: "teams_chunck" */ '@/apps/teams/app/TeamTypeBrowse.vue'),
        name : 'teamtype.browse',
    }
];
