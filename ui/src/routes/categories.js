import App from '@/site/App.vue';

const CategoryHeader = () => import(
  /* webpackChunkName: "category_chunck" */
  '@/apps/categories/TheCategoryHeader.vue'
);
const CategoryRead = () => import(
  /* webpackChunkName: "category_chunck" */
  '@/apps/categories/CategoryRead.vue'
);
const CategoryFormHeader = () => import(
  /* webpackChunkName: "category_admin_chunck" */
  '@/apps/categories/TheCategoryFormHeader.vue'
);
const CategoryForm = () => import(
  /* webpackChunkName: "category_admin_chunck" */
  '@/apps/categories/CategoryForm.vue'
);
const CategoriesHeader = () => import(
  /* webpackChunkName: "category_chunck" */
  '@/apps/categories/TheCategoriesHeader.vue'
);
const CategoryBrowse = () => import(
  /* webpackChunkName: "category_chunck" */
  '@/apps/categories/CategoryBrowse.vue'
);

const CategoryStore = () =>
  import(/* webpackChunkName: "category_chunck" */ '@/stores/categories'
  );
const NewsStore = () =>
  import(/* webpackChunkName: "news_chunck" */ '@/stores/news'
  );
const PageStore = () =>
  import(/* webpackChunkName: "pages_chunck" */ '@/stores/pages'
  );

export default [
  {
    path: '/categories',
    component: App,
    meta: {
      stores: [
        {
          ns: [ 'category' ],
          create: CategoryStore
        },
      ]
    },
    children: [
      {
        path: ':id(\\d+)',
        meta: {
          stores: [
            {
              ns: [ 'news' ],
              create: NewsStore
            },
            {
              ns: [ 'page' ],
              create: PageStore
            },
          ]
        },
        components: {
          header: CategoryHeader,
          main: CategoryRead
        },
        name: 'categories.read',
      },
      {
        path: 'create',
        components: {
          header: CategoryFormHeader,
          main: CategoryForm
        },
        props: {
          header: {
            creating: true
          }
        },
        name: 'categories.create',
      },
      {
        path: 'update/:id(\\d+)',
        components: {
          header: CategoryFormHeader,
          main: CategoryForm
        },
        props: {
          header: {
            creating: true
          }
        },
        name: 'categories.update',
      },
      {
        path: '',
        components: {
          header: CategoriesHeader,
          main: CategoryBrowse
        },
        name: 'categories.browse',
      },
    ]
  },
];
