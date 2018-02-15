export default [
    {
        path : '/users/',
        component : () => import(/* webpackChunkName: "user_admin" */ '@/apps/users/App.vue'),
        children : [
            {
                path : '/users/:id',
                components : {
                    UserContent : () => import(/* webpackChunkName: "user_admin" */ '@/apps/users/app/UserRead.vue')
                },
                props : {
                    UserContent : true
                },
                name : 'users.read'
            },
            {
                path : '',
                components : {
                    UserContent : () => import(/* webpackChunkName: "user_admin" */ '@/apps/users/app/UserBrowse.vue')
                },
                name : 'users.browse'
            }
        ]
    }
];
