import App from '@/site/App';

const MembersHeader = () => import(
  /* webpackChunkName: "member_admin" */ '@/apps/members/MembersHeader.vue'
);
const MemberRead = () => import(
  /* webpackChunkName: "member_admin" */ '@/apps/members/MemberRead.vue'
);
const MemberUploadHeader = () => import(
  /* webpackChunkName: "member_admin" */ '@/apps/members/MemberUploadHeader.vue'
);
const MemberUpload = () => import(
  /* webpackChunkName: "member_admin" */ '@/apps/members/MemberUpload.vue'
);
const MemberBrowse = () => import(
  /* webpackChunkName: "member_admin" */ '@/apps/members/MemberBrowse.vue'
);

export default [
  {
    path: '/members',
    component: App,
    children: [
      {
        path: ':id(\\d+)',
        components: {
          main: MemberRead
        },
        name: 'members.read',
      },
      {
        path: 'upload',
        components: {
          header: MemberUploadHeader,
          main: MemberUpload
        },
        name: 'members.upload',
      },
      {
        path: '',
        components: {
          header: MembersHeader,
          main: MemberBrowse
        },
        name: 'members.browse',
      },
    ]
  },
];
