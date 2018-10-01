export default [
    {
        path : '/categories/:id(\\d+)',
        component : () => import(/* webpackChunkName: "category_chunck" */ '@/apps/categories/CategoryRead.vue'),
        name : 'categories.read',
    },
    {
        path : '/categories/create',
        component : () => import(/* webpackChunkName: "category_admin" */ '@/apps/categories/CategoryForm.vue'),
        name : 'categories.create'
    },
    {
        path : '/categories/update/:id(\\d+)',
        component : () => import(/* webpackChunkName: "category_admin" */ '@/apps/categories/CategoryForm.vue'),
        name : 'categories.update'
    },
    {
        path : '/categories',
        component : () => import(/* webpackChunkName: "category_chunck" */ '@/apps/categories/CategoryBrowse.vue'),
        name : 'categories.browse'
    }
];
