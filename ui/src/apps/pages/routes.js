import App from './App.vue';

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

export default [
  {
    path: '/pages',
    component: App,
    children: [
      {
        path: ':id(\\d+)',
        components: {
          hero: PageHeader,
          default: PageRead
        },
        name: 'pages.read',
      },
      {
        path: 'category/:category(\\d+)',
        components: {
          hero: PageCategoryHeader,
          default: PageBrowse
        },
        name: 'pages.category',
      },
      {
        path: 'create',
        components: {
          hero: PageFormHeader,
          default: PageForm
        },
        props: {
          hero: {
            creating: true
          }
        },
        name: 'pages.create',
      },
      {
        path: 'update/:id(\\d+)',
        name: 'pages.update',
        components: {
          hero: PageFormHeader,
          default: PageForm
        },
        props: {
          hero: {
            creating: false
          }
        },
      },
      {
        path: '',
        components: {
          hero: PagesHeader,
          default: PageBrowse
        },
        name: 'pages.browse',
      },
    ]
  },
];
