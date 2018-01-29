export default [
    {
        path : '/categories/',
        component : () => import(/* webpackChunkName: "category_admin" */ '@/apps/categories/App.vue'),
        name : 'categories'
    },
    {
        path : '/categories/create',
        component : () => import(/* webpackChunkName: "category_admin" */ '@/apps/categories/app/CategoryCreate.vue'),
        name : 'categories.create'
    },
    {
        path : '/categories/update/:id(\\d+)',
        component : () => import(/* webpackChunkName: "category_admin" */ '@/apps/categories/app/CategoryUpdate.vue'),
        name : 'categories.update'
    },
    {
        path : '/categories/:id(\\d+)',
        component : () => import(/* webpackChunkName: "category_admin" */ '@/apps/categories/app/CategoryRead.vue'),
        name : 'categories.read'
    }
];
