export default [
    {
        path : '/members/:id',
        component : () => import(/* webpackChunkName: "member_admin" */ '@/apps/members/app/MemberRead.vue'),
        name : 'members.read'
    },
    {
        path : '/members',
        component : () => import(/* webpackChunkName: "member_admin" */ '@/apps/members/app/MemberBrowse.vue'),
        name : 'members.browse'
    }
];
