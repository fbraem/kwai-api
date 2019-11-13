import Site from './Site';
import Toolbar from './Toolbar';
import HomeApp from './Home';
import Header from './Header';

let routes = {
  path: '/',
  components: {
    toolbar: Toolbar,
    default: Site
  },
};

import NewsStore from '@/stores/news';

import makeStore from '@/js/makeVuex';
var store = makeStore();

import makeRoutes from '@/apps/routes';
routes.children = makeRoutes();
routes.children.push(
  {
    path: '',
    name: 'home',
    beforeEnter(to, from, next) {
      store.setModule(['news'], NewsStore);
      next();
    },
    components: {
      hero: Header,
      default: HomeApp
    }
  }
);

export default [
  routes,
];
