export default [
        {
            path : '/news/',
            components : {
                default : () => import(/* webpackChunkName: "news_chunck" */ '@/apps/news/App.vue')
            },
            children: [
                {
                    path : 'story/:id(\\d+)',
                    components : {
                        NewsContent : () => import(/* webpackChunkName: "news_chunck" */ '@/apps/news/app/NewsRead.vue')
                    },
                    name : 'news.story',
                    props : {
                        NewsContent : true
                    }
                },
                {
                    path : 'category/:category_id(\\d+)',
                    components : {
                        NewsContent : () => import(/* webpackChunkName: "news_chunck" */ '@/apps/news/app/NewsBrowse.vue')
                    },
                    name : 'news.category',
                    props : {
                        NewsContent : true
                    }
                },
                {
                    path : 'archive/:year(\\d+)/:month(\\d+)',
                    components : {
                        NewsContent : () => import(/* webpackChunkName: "news_chunck" */ '@/apps/news/app/NewsBrowse.vue')
                    },
                    name : 'news.archive',
                    props : {
                        NewsContent : true
                    }
                },
                {
                    path : '',
                    components : {
                        NewsContent : () => import(/* webpackChunkName: "news_chunck" */ '@/apps/news/app/NewsBrowse.vue')
                    },
                    name : 'news.browse'
                }
            ]
        },
        {
            path : '/news/create',
            component : () => import(/* webpackChunkName: "news_admin" */ '@/apps/news/app/NewsCreate.vue'),
            name : 'news.create'
        },
        {
            path : '/news/update/:id(\\d+)',
            component : () => import(/* webpackChunkName: "news_admin" */ '@/apps/news/app/NewsUpdate.vue'),
            name : 'news.update'
        }
];
