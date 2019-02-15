import App from '@/site/App.vue';

export default [
  {
    path: '/categories',
    component: App,
    children: [
      {
        path: ':id(\\d+)',
        components: {
          header: () => import(/* webpackChunkName: "category_chunck" */ '@/apps/categories/CategoryHeader.vue'
          ),
          main: () => import(
            /* webpackChunkName: "category_chunck" */ '@/apps/categories/CategoryRead.vue'
          )
        },
        name: 'categories.read',
      },
      {
        path: 'create',
        components: {
          header: () => import(/* webpackChunkName: "category_chunck" */ '@/apps/categories/CategoryFormHeader.vue'
          ),
          main: () => import(
            /* webpackChunkName: "category_admin" */ '@/apps/categories/CategoryForm.vue'
          )
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
          header: () => import(/* webpackChunkName: "category_chunck" */ '@/apps/categories/CategoryFormHeader.vue'
          ),
          main: () => import(
          /* webpackChunkName: "category_admin" */ '@/apps/categories/CategoryForm.vue'
          )
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
          header: () => import(/* webpackChunkName: "category_chunck" */ '@/apps/categories/CategoriesHeader.vue'
          ),
          main: () => import(
            /* webpackChunkName: "category_chunck" */ '@/apps/categories/CategoryBrowse.vue'
          )
        },
        name: 'categories.browse',
      },
    ]
  }
];
