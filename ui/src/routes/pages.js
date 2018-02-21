export default [
        {
            path : '/pages/',
            components : {
                default : () => import(/* webpackChunkName: "pages_chunck" */ '@/apps/pages/App.vue')
            },
            children: [
                {
                    path : ':id(\\d+)',
                    components : {
                        PageContent : () => import(/* webpackChunkName: "pages_chunck" */ '@/apps/pages/app/PageRead.vue')
                    },
                    name : 'pages.read',
                    props : {
                        PageContent : true
                    }
                },
                {
                    path : 'category/:category_id(\\d+)',
                    components : {
                        PageContent : () => import(/* webpackChunkName: "pages_chunck" */ '@/apps/pages/app/PageBrowse.vue')
                    },
                    name : 'pages.category',
                    props : {
                        PageContent : true
                    }
                },
                {
                    path : '',
                    components : {
                        PageContent : () => import(/* webpackChunkName: "pages_chunck" */ '@/apps/pages/app/PageBrowse.vue')
                    },
                    name : 'pages'
                }
            ]
        },
        {
            path : '/pages/create',
            component : () => import(/* webpackChunkName: "pages_admin" */ '@/apps/pages/app/PageCreate.vue'),
            name : 'pages.create'
        },
        {
            path : '/pages/update/:id(\\d+)',
            component : () => import(/* webpackChunkName: "pages_admin" */ '@/apps/pages/app/PageUpdate.vue'),
            name : 'pages.update'
        }
];
