import App from '@/site/App.vue';

export default [
  {
    path: '/news',
    component: App,
    children: [
      {
        path: ':id(\\d+)',
        components: {
          header: () => import(
            /* webpackChunkName: "news_chunck" */ '@/apps/news/NewsStoryHeader.vue'
          ),
          main: () => import(
            /* webpackChunkName: "news_chunck" */ '@/apps/news/NewsRead.vue')
        },
        name: 'news.story',
      },
      {
        path: 'category/:category(\\d+)',
        components: {
          header: () => import(
            /* webpackChunkName: "news_chunck" */ '@/apps/news/NewsCategoryHeader.vue'
          ),
          main: () => import(
            /* webpackChunkName: "news_chunck" */ '@/apps/news/NewsBrowse.vue'
          )
        },
        name: 'news.category',
      },
      {
        path: 'archive/:year(\\d+)/:month(\\d+)',
        components: {
          header: () => import(
            /* webpackChunkName: "news_chunck" */ '@/apps/news/NewsArchiveHeader.vue'
          ),
          main: () => import(
            /* webpackChunkName: "news_chunck" */ '@/apps/news/NewsBrowse.vue'
          )
        },
        name: 'news.archive',
      },
      {
        path: 'create',
        components: {
          header: () => import(
            /* webpackChunkName: "news_admin" */ '@/apps/news/NewsFormHeader.vue'
          ),
          main: () => import(
            /* webpackChunkName: "news_admin" */ '@/apps/news/NewsForm.vue'
          )
        },
        props: {
          header: {
            creating: true
          }
        },
        name: 'news.create',
      },
      {
        path: 'update/:id(\\d+)',
        components: {
          header: () => import(
            /* webpackChunkName: "news_admin" */ '@/apps/news/NewsFormHeader.vue'
          ),
          main: () => import(
            /* webpackChunkName: "news_admin" */ '@/apps/news/NewsForm.vue'
          )
        },
        props: {
          header: {
            creating: false
          }
        },
        name: 'news.update',
      },
      {
        path: '',
        components: {
          main: () => import(
            /* webpackChunkName: "news_chunck" */ '@/apps/news/NewsBrowse.vue'
          )
        },
        name: 'news.browse',
      },
    ]
  },
];
