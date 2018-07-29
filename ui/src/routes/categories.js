export default [
    {
        path : '/categories/',
        component : () => import(/* webpackChunkName: "category_chunk" */ '@/apps/categories/App.vue'),
        children : [
            {
                path : ':id(\\d+)',
                components : {
                    CategoryContent : () => import(/* webpackChunkName: "category_chunk" */ '@/apps/categories/app/CategoryRead.vue')
                },
                name : 'categories.read',
                props : {
                    CategoryComponent : true
                }
            },
            {
                path : '',
                components : {
                    CategoryContent : () => import(/* webpackChunkName: "category_chunck" */ '@/apps/categories/app/CategoryBrowse.vue')
                },
                name : 'categories.browse'
            }
        ]
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
    }
];
