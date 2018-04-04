export default [
        {
            path : '/teams/',
            component : () => import(/* webpackChunkName: "teams_chunck" */ '@/apps/teams/App.vue'),
            children: [
                {
                    path : '',
                    components : {
                        ListContent : () => import(/* webpackChunkName: "teams_chunck" */ '@/apps/teams/app/TeamBrowse.vue')
                    },
                    name : 'team.browse',
                    children : [
                        {
                            path : ':id(\\d+)',
                            components : {
                                TeamContent : () => import(/* webpackChunkName: "teams_chunck" */ '@/apps/teams/app/TeamRead.vue')
                            },
                            name : 'team.read',
                            props : {
                                TeamContent : true
                            }
                        },
                        {
                            path : 'create',
                            components : {
                                TeamContent : () => import(/* webpackChunkName: "teams_admin" */ '@/apps/teams/app/TeamCreate.vue')
                            },
                            name : 'team.create'
                        },
                        {
                            path : 'update/:id(\\d+)',
                            components : {
                                TeamContent : () => import(/* webpackChunkName: "teams_admin" */ '@/apps/teams/app/TeamUpdate.vue')
                            },
                            name : 'team.update',
                            props : {
                                TeamContent : true
                            }
                        }
                    ]
                },
                {
                    path : 'types/:id(\\d+)',
                    components : {
                        TeamContent : () => import(/* webpackChunkName: "teams_chunck" */ '@/apps/teams/app/TeamTypeRead.vue')
                    },
                    name : 'teamtype.read',
                    props : {
                        TeamContent : true
                    }
                },
                {
                    path : 'types/create',
                    components : {
                        TeamContent : () => import(/* webpackChunkName: "teams_admin" */ '@/apps/teams/app/TeamTypeCreate.vue')
                    },
                    name : 'teamtype.create'
                },
                {
                    path : 'types/update/:id(\\d+)',
                    components : {
                        TeamContent : () => import(/* webpackChunkName: "teams_admin" */ '@/apps/teams/app/TeamTypeUpdate.vue')
                    },
                    name : 'teamtype.update',
                    props : {
                        TeamContent : true
                    }
                },
                {
                    path : 'types',
                    components : {
                        ListContent : () => import(/* webpackChunkName: "teams_chunck" */ '@/apps/teams/app/TeamTypeBrowse.vue')
                    },
                    name : 'teamtype.browse',
                    props : {
                        TeamContent : true
                    }
                }
            ]
        },
];
