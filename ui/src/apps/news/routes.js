export default [
        {
            path : '/news/',
            components : {
                default : () => import(/* webpackChunkName: "news_chunck" */ './App.vue')
            },
            children: [
                {
                    path : 'story/:id',
                    components : {
                        NewsContent : () => import(/* webpackChunkName: "news_chunck" */ './app/NewsRead.vue')
                    },
                    name : 'news.story',
                    props : {
                        NewsContent : true
                    }
                },
                {
                    path : 'category/:category_id',
                    components : {
                        NewsContent : () => import(/* webpackChunkName: "news_chunck" */ './app/NewsBrowse.vue')
                    },
                    name : 'news.category',
                    props : {
                        NewsContent : true
                    }
                },
                {
                    path : 'archive/:year/:month',
                    components : {
                        NewsContent : () => import(/* webpackChunkName: "news_chunck" */ './app/NewsBrowse.vue')
                    },
                    name : 'news.archive',
                    props : {
                        NewsContent : true
                    }
                },
                {
                    path : '',
                    components : {
                        NewsContent : () => import(/* webpackChunkName: "news_chunck" */ './app/NewsBrowse.vue')
                    },
                    name : 'news.browse'
                }
            ]
        },
        {
            path : '/news/create',
            component : () => import(/* webpackChunkName: "news_admin" */ './app/NewsCreate.vue'),
            name : 'news.create'
        },
        {
            path : '/news/update/:id',
            component : () => import(/* webpackChunkName: "news_admin" */ './app/NewsUpdate.vue'),
            name : 'news.update'
        }
];
