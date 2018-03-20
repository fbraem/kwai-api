export default [
        {
            path : '/seasons/',
            component : () => import(/* webpackChunkName: "seasons_chunck" */ '@/apps/seasons/App.vue'),
            children: [
                {
                    path : ':id(\\d+)',
                    components : {
                        SeasonContent : () => import(/* webpackChunkName: "seasons_chunck" */ '@/apps/seasons/app/SeasonRead.vue')
                    },
                    name : 'season.read',
                    props : {
                        SeasonContent : true
                    }
                },
                {
                    path : 'create',
                    components : {
                        SeasonContent : () => import(/* webpackChunkName: "seasons_admin" */ '@/apps/seasons/app/SeasonCreate.vue')
                    },
                    name : 'season.create'
                },
                {
                    path : 'update/:id(\\d+)',
                    components : {
                        SeasonContent : () => import(/* webpackChunkName: "seasons_admin" */ '@/apps/seasons/app/SeasonUpdate.vue')
                    },
                    name : 'season.update',
                    props : {
                        SeasonContent : true
                    }
                },
                {
                    path : '',
                    components : {
                        SeasonContent : () => import(/* webpackChunkName: "seasons_chunck" */ '@/apps/seasons/app/SeasonBrowse.vue')
                    },
                    name : 'season.browse'
                }
            ]
        },
];
