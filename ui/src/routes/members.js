export default [
    {
        path : '/members/:id',
        component : () => import(/* webpackChunkName: "member_admin" */ '@/apps/members/MemberRead.vue'),
        name : 'members.read'
    },
    {
        path : '/members',
        component : () => import(/* webpackChunkName: "member_admin" */ '@/apps/members/MemberBrowse.vue'),
        name : 'members.browse'
    }
];
