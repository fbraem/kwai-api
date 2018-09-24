export default [
    {
        path : '/users/invite',
        component : () => import(/* webpackChunkName: "user_admin" */ '@/apps/users/UserForm.vue'),
        name : 'users.create'
    },
    {
        path : '/users/invite/:token',
        component : () => import(/* webpackChunkName: "user_admin" */ '@/apps/users/UserInvite.vue'),
        name : 'users.invite'
    },
    {
        path : '/users/:id',
        component : () => import(/* webpackChunkName: "user_admin" */ '@/apps/users/UserRead.vue'),
        name : 'users.read'
    },
    {
        path : '/users',
        component : () => import(/* webpackChunkName: "user_admin" */ '@/apps/users/UserBrowse.vue'),
        name : 'users.browse'
    }
];
