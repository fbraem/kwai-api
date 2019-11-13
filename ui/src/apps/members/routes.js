import App from './App';

const MembersHeader = () => import(
  /* webpackChunkName: "member_admin" */
  '@/apps/members/TheMembersHeader.vue'
);

const MemberHeader = () => import(
  /* webpackChunkName: "member_admin" */
  '@/apps/members/TheMemberHeader.vue'
);
const MemberRead = () => import(
  /* webpackChunkName: "member_admin" */
  '@/apps/members/MemberRead.vue'
);
const MemberDetail = () => import(
  /* webpackChunkName: "member_admin" */
  '@/apps/members/MemberDetail.vue'
);
const NotImplemented = () => import(
  /* webpackChunkName: "member_admin" */
  '@/apps/members/NotImplemented.vue'
);
const MemberTeams = () => import(
  /* webpackChunkName: "member_admin" */
  '@/apps/members/MemberTeams.vue'
);
const MemberUploadHeader = () => import(
  /* webpackChunkName: "member_admin" */
  '@/apps/members/TheMemberUploadHeader.vue'
);
const MemberUpload = () => import(
  /* webpackChunkName: "member_admin" */
  '@/apps/members/MemberUpload.vue'
);
const MemberBrowse = () => import(
  /* webpackChunkName: "member_admin" */
  '@/apps/members/MemberBrowse.vue'
);

export default [
  {
    path: '/members',
    component: App,
    children: [
      {
        path: ':id(\\d+)',
        components: {
          hero: MemberHeader,
          default: MemberRead
        },
        children: [
          {
            path: 'teams',
            components: {
              member_information: MemberTeams
            },
            name: 'members.teams'
          },
          {
            path: 'trainings',
            components: {
              member_information: NotImplemented,
            },
            name: 'members.trainings',
          },
          {
            path: 'tournaments',
            components: {
              member_information: NotImplemented,
            },
            name: 'members.tournaments',
          },
          {
            path: '',
            components: {
              member_information: MemberDetail,
            },
            name: 'members.read',
          },
        ]
      },
      {
        path: 'upload',
        components: {
          hero: MemberUploadHeader,
          default: MemberUpload
        },
        name: 'members.upload',
      },
      {
        path: '',
        components: {
          hero: MembersHeader,
          default: MemberBrowse
        },
        name: 'members.browse',
      },
    ]
  },
];
