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

import makeStore from '@/js/makeVuex';
var store = makeStore();

export default [
  {
    path: '/categories',
    component: App,
    async beforeEnter(to, from, next) {
      if (!to.meta.called) {
        to.meta.called = true;
        await store.setModule(['category'], CategoryStore);
      }
      next();
    },
    children: [
      {
        path: ':id(\\d+)',
        async beforeEnter(to, from, next) {
          await store.dispatch('category/read', { id: to.params.id });
          const category = store.getters['category/category'](to.params.id);
          if (category.app) {
            next({
              path: '/' + category.app
            });
            return;
          }
          await store.setModule(['news'], NewsStore);
          await store.setModule(['page'], PageStore);
          next();
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
