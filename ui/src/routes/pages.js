import App from '@/site/App.vue';

const PagesHeader = () => import(
  /* webpackChunkName: "pages_chunck" */
  '@/apps/pages/ThePagesHeader.vue'
);
const PageHeader = () => import(
  /* webpackChunkName: "pages_chunck" */
  '@/apps/pages/ThePageHeader.vue'
);
const PageCategoryHeader = () => import(
  /* webpackChunkName: "pages_chunck" */
  '@/apps/pages/ThePageCategoryHeader.vue'
);
const PageFormHeader = () => import(
  /* webpackChunkName: "pages_chunck" */
  '@/apps/pages/ThePageFormHeader.vue'
);
const PageRead = () => import(
  /* webpackChunkName: "pages_chunck" */
  '@/apps/pages/PageRead.vue'
);
const PageBrowse = () => import(
  /* webpackChunkName: "pages_chunck" */
  '@/apps/pages/PageBrowse.vue'
);
const PageForm = () => import(
  /* webpackChunkName: "pages_admin" */
  '@/apps/pages/PageForm.vue'
);

import PageStore from '@/stores/pages';

import makeStore from '@/js/makeVuex';
var store = makeStore();

export default [
  {
    path: '/pages',
    beforeEnter(to, from, next) {
      store.setModule(['page'], PageStore);
      next();
    },
    component: App,
    children: [
      {
        path: ':id(\\d+)',
        components: {
          header: PageHeader,
          main: PageRead
        },
        name: 'pages.read',
      },
      {
        path: 'category/:category(\\d+)',
        components: {
          header: PageCategoryHeader,
          main: PageBrowse
        },
        name: 'pages.category',
      },
      {
        path: 'create',
        components: {
          header: PageFormHeader,
          main: PageForm
        },
        props: {
          header: {
            creating: true
          }
        },
        name: 'pages.create',
      },
      {
        path: 'update/:id(\\d+)',
        name: 'pages.update',
        components: {
          header: PageFormHeader,
          main: PageForm
        },
        props: {
          header: {
            creating: false
          }
        },
      },
      {
        path: '',
        components: {
          header: PagesHeader,
          main: PageBrowse
        },
        name: 'pages.browse',
      },
    ]
  },
];
