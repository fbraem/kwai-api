export default [
    {
        path : '/members/:id(\\d+)',
        component : () => import(/* webpackChunkName: "member_admin" */ '@/apps/members/MemberRead.vue'),
        name : 'members.read'
    },
    {
        path : '/members/upload',
        component : () => import(/* webpackChunkName: "member_admin" */ '@/apps/members/MemberUpload.vue'),
        name : 'members.upload'
    },
    {
        path : '/members',
        component : () => import(/* webpackChunkName: "member_admin" */ '@/apps/members/MemberBrowse.vue'),
        name : 'members.browse'
    }
];
