import Site from './Site';
import Toolbar from './Toolbar';
import App from './App';
import HomeApp from './Home';
import Header from './Header';

let routes = {
  path: '/',
  components: {
    toolbar: Toolbar,
    default: Site
  },
};

import makeRoutes from '@/apps/routes';
routes.children = makeRoutes();
routes.children.push(
  {
    path: '',
    component: App,
    children: [
      {
        path: '',
        name: 'home',
        components: {
          hero: Header,
          default: HomeApp
        }
      },
    ]
  }
);

export default [
  routes,
];
