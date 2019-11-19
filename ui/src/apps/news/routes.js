import App from './App.vue';

const NewsHeader = () => import(
  /* webpackChunkName: "news_chunck" */
  '@/apps/news/TheNewsHeader.vue'
);
const NewsStoryHeader = () => import(
  /* webpackChunkName: "news_chunck" */
  '@/apps/news/TheNewsStoryHeader.vue'
);
const NewsCategoryHeader = () => import(
  /* webpackChunkName: "news_chunck" */
  '@/apps/news/TheNewsCategoryHeader.vue'
);
const NewsArchiveHeader = () => import(
  /* webpackChunkName: "news_chunck" */
  '@/apps/news/TheNewsArchiveHeader.vue'
);
const NewsFormHeader = () => import(
  /* webpackChunkName: "news_admin" */
  '@/apps/news/TheNewsFormHeader.vue'
);
const NewsRead = () => import(
  /* webpackChunkName: "news_chunck" */
  '@/apps/news/NewsRead.vue'
);
const NewsBrowse = () => import(
  /* webpackChunkName: "news_chunck" */
  '@/apps/news/NewsBrowse.vue'
);
const NewsForm = () => import(
  /* webpackChunkName: "news_admin" */
  '@/apps/news/NewsForm.vue'
);

export default [
  {
    path: '/news',
    component: App,
    children: [
      {
        path: ':id(\\d+)',
        components: {
          hero: NewsStoryHeader,
          default: NewsRead
        },
        name: 'news.story',
      },
      {
        path: 'category/:category(\\d+)',
        components: {
          hero: NewsCategoryHeader,
          default: NewsBrowse
        },
        name: 'news.category',
      },
      {
        path: 'archive/:year(\\d+)/:month(\\d+)',
        components: {
          hero: NewsArchiveHeader,
          default: NewsBrowse
        },
        name: 'news.archive',
      },
      {
        path: 'create',
        components: {
          hero: NewsFormHeader,
          default: NewsForm
        },
        props: {
          hero: {
            creating: true
          }
        },
        name: 'news.create',
      },
      {
        path: 'update/:id(\\d+)',
        components: {
          hero: NewsFormHeader,
          default: NewsForm
        },
        props: {
          hero: {
            creating: false
          }
        },
        name: 'news.update',
      },
      {
        path: '',
        components: {
          hero: NewsHeader,
          default: NewsBrowse
        },
        name: 'news.browse',
      },
    ]
  },
];
