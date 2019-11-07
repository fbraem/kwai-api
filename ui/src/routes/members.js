import App from '@/site/App';

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

import MemberStore from '@/stores/members';

import makeStore from '@/js/makeVuex';
var store = makeStore();

export default [
  {
    path: '/members',
    component: App,
    beforeEnter(to, from, next) {
      store.setModule(['member'], MemberStore);
      next();
    },
    children: [
      {
        path: ':id(\\d+)',
        components: {
          header: MemberHeader,
          main: MemberRead
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
