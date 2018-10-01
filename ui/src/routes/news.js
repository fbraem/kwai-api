export default [
    {
        path : '/news/:id(\\d+)',
        component : () => import(/* webpackChunkName: "news_chunck" */ '@/apps/news/NewsRead.vue'),
        name : 'news.story'
    },
    {
        path : '/news/category/:category(\\d+)',
        component : () => import(/* webpackChunkName: "news_chunck" */ '@/apps/news/NewsBrowse.vue'),
        name : 'news.category',
    },
    {
        path : '/news/archive/:year(\\d+)/:month(\\d+)',
        component : () => import(/* webpackChunkName: "news_chunck" */ '@/apps/news/NewsBrowse.vue'),
        name : 'news.archive'
    },
    {
        path : '/news/create',
        component : () => import(/* webpackChunkName: "news_admin" */ '@/apps/news/NewsForm.vue'),
        name : 'news.create'
    },
    {
        path : '/news/update/:id(\\d+)',
        component : () => import(/* webpackChunkName: "news_admin" */ '@/apps/news/NewsForm.vue'),
        name : 'news.update'
    },
    {
        path : '/news',
        component : () => import(/* webpackChunkName: "news_chunck" */ '@/apps/news/NewsBrowse.vue'),
        name : 'news.browse'
    }
];
