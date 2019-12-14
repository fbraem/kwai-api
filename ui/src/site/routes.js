import Site from './Site';
import Toolbar from './Toolbar';
import App from './App';
import HomeApp from './Home';
import Header from './Header';
import Footer from './Footer';

let routes = {
  path: '/',
  components: {
    toolbar: Toolbar,
    default: Site,
    footer: Footer
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
