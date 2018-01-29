export default [
    {
        path : '/categories/',
        component : () => import(/* webpackChunkName: "category_admin" */ '@/apps/categories/App.vue'),
        name : 'category'
    },
    {
        path : '/categories/create',
        component : () => import(/* webpackChunkName: "category_admin" */ '@/apps/categories/app/CategoryCreate.vue'),
        name : 'category.create'
    },
    {
        path : '/categories/update/:id',
        component : () => import(/* webpackChunkName: "category_admin" */ '@/apps/categories/app/CategoryUpdate.vue'),
        name : 'category.update'
    },
    {
        path : '/categories/read/:id',
        component : () => import(/* webpackChunkName: "category_admin" */ '@/apps/categories/app/CategoryRead.vue'),
        name : 'category.read'
    }
];
