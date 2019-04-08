import App from '@/site/App.vue';
import Header from '@/site/Header.vue';
import SiteApp from '@/site/Home.vue';

const CategoryStore = () =>
  import(/* webpackChunkName: "category_chunck" */ '@/stores/categories'
  );
const NewsStore = () =>
  import(/* webpackChunkName: "news_chunck" */ '@/stores/news'
  );

import makeStore from '@/js/makeVuex';
var store = makeStore();

export default [
  {
    path: '/',
    component: App,
    children: [
      {
        path: '',
        name: 'home',
        async beforeEnter(to, from, next) {
          await store.setModule(['category'], CategoryStore);
          await store.setModule(['news'], NewsStore);
          next();
        },
        components: {
          header: Header,
          main: SiteApp
        }
      },
    ]
  },
];
