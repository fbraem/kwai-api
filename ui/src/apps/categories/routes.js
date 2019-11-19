import App from './App.vue';

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

export default [
  {
    path: '/categories',
    component: App,
    children: [
      {
        path: ':id(\\d+)',
        components: {
          hero: CategoryHeader,
          default: CategoryRead
        },
        name: 'categories.read',
      },
      {
        path: 'create',
        components: {
          hero: CategoryFormHeader,
          default: CategoryForm
        },
        props: {
          hero: {
            creating: true
          }
        },
        name: 'categories.create',
      },
      {
        path: 'update/:id(\\d+)',
        components: {
          hero: CategoryFormHeader,
          default: CategoryForm
        },
        props: {
          hero: {
            creating: false
          }
        },
        name: 'categories.update',
      },
      {
        path: '',
        components: {
          hero: CategoriesHeader,
          default: CategoryBrowse
        },
        name: 'categories.browse',
      },
    ]
  },
];
