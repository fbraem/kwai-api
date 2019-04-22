import App from '@/site/App.vue';

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

import NewsStore from '@/stores/news';

import makeStore from '@/js/makeVuex';
var store = makeStore();

export default [
  {
    path: '/news',
    beforeEnter(to, from, next) {
      store.setModule(['news'], NewsStore);
      next();
    },
    component: App,
    children: [
      {
        path: ':id(\\d+)',
        components: {
          header: NewsStoryHeader,
          main: NewsRead
        },
        name: 'news.story',
      },
      {
        path: 'category/:category(\\d+)',
        components: {
          header: NewsCategoryHeader,
          main: NewsBrowse
        },
        name: 'news.category',
      },
      {
        path: 'archive/:year(\\d+)/:month(\\d+)',
        components: {
          header: NewsArchiveHeader,
          main: NewsBrowse
        },
        name: 'news.archive',
      },
      {
        path: 'create',
        components: {
          header: NewsFormHeader,
          main: NewsForm
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
          header: NewsFormHeader,
          main: NewsForm
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
          header: NewsHeader,
          main: NewsBrowse
        },
        name: 'news.browse',
      },
    ]
  },
];
