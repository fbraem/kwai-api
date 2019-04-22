import App from '@/site/App.vue';
import Header from '@/site/Header.vue';
import SiteApp from '@/site/Home.vue';

import NewsStore from '@/stores/news';

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
        beforeEnter(to, from, next) {
          store.setModule(['news'], NewsStore);
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
