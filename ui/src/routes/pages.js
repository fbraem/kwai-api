export default [
    {
        path : '/pages/:id(\\d+)',
        component : () => import(/* webpackChunkName: "pages_chunck" */ '@/apps/pages/PageRead.vue'),
        name : 'pages.read'
    },
    {
        path : '/pages/category/:category_id(\\d+)',
        component : () => import(/* webpackChunkName: "pages_chunck" */ '@/apps/pages/PageBrowse.vue'),
        name : 'pages.category'
    },
    {
        path : '/pages/create',
        component : () => import(/* webpackChunkName: "pages_admin" */ '@/apps/pages/PageForm.vue'),
        name : 'pages.create'
    },
    {
        path : '/pages/update/:id(\\d+)',
        component : () => import(/* webpackChunkName: "pages_admin" */ '@/apps/pages/PageForm.vue'),
        name : 'pages.update'
    },
    {
        path : '/pages',
        component : () => import(/* webpackChunkName: "pages_chunck" */ '@/apps/pages/PageBrowse.vue'),
        name : 'pages.browse'
    }
];
