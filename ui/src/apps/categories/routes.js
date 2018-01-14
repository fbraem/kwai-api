export default [
    {
        path : '/categories/',
        component : () => import(/* webpackChunkName: "category_admin" */ './App.vue'),
        name : 'category'
    },
    {
        path : '/categories/create',
        component : () => import(/* webpackChunkName: "category_admin" */ './app/CategoryCreate.vue'),
        name : 'category.create'
    },
    {
        path : '/categories/update/:id',
        component : () => import(/* webpackChunkName: "category_admin" */ './app/CategoryUpdate.vue'),
        name : 'category.update'
    },
    {
        path : '/categories/read/:id',
        component : () => import(/* webpackChunkName: "category_admin" */ './app/CategoryRead.vue'),
        name : 'category.read'
    }
];
