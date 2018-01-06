export default [
    {
        path : '/categories/',
        component : () => import(/* webpackChunkName: "category_admin" */ './App.vue')
    },
    {
        path : '/categories/create',
        component : () => import(/* webpackChunkName: "category_admin" */ './app/CategoryCreate.vue')
    },
    {
        path : '/categories/update/:id',
        component : () => import(/* webpackChunkName: "category_admin" */ './app/CategoryUpdate.vue')
    },
    {
        path : '/categories/read/:id',
        component : () => import(/* webpackChunkName: "category_admin" */ './app/CategoryRead.vue')
    }
];
