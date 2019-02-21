import App from '@/site/App.vue';
import Header from '@/site/Header.vue';
import SiteApp from '@/site/Home.vue';

const CategoryStore = () =>
  import(/* webpackChunkName: "category_chunck" */ '@/stores/categories'
  );
const NewsStore = () =>
  import(/* webpackChunkName: "news_chunck" */ '@/stores/news'
  );

export default [
  {
    path: '/',
    component: App,
    children: [
      {
        name: 'home',
        meta: {
          stores: [
            {
              ns: [ 'category'],
              create: CategoryStore
            },
            {
              ns: [ 'news'],
              create: NewsStore
            },
          ]
        },
        path: '',
        components: {
          header: Header,
          main: SiteApp
        }
      },
    ]
  },
];
