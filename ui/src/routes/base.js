import App from '@/site/App.vue';
import Header from '@/site/Header.vue';
import SiteApp from '@/site/Home.vue';

export default [
  {
    path: '/',
    component: App,
    children: [
      {
        name: 'home',
        path: '',
        components: {
          header: Header,
          main: SiteApp
        }
      },
    ]
  },
];
