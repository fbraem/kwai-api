export default [
    {
        path : '/members/',
        component : () => import(/* webpackChunkName: "member_admin" */ '@/apps/members/App.vue'),
        children : [
            {
                path : '/members/:id',
                components : {
                    MemberContent : () => import(/* webpackChunkName: "member_admin" */ '@/apps/members/app/MemberRead.vue')
                },
                props : {
                    MemberContent : true
                },
                name : 'members.read'
            },
            {
                path : '',
                components : {
                    MemberContent : () => import(/* webpackChunkName: "member_admin" */ '@/apps/members/app/MemberBrowse.vue')
                },
                name : 'members.browse'
            }
        ]
    }
];
