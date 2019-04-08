import App from '@/site/App';

const MembersHeader = () => import(
  /* webpackChunkName: "member_admin" */
  '@/apps/members/TheMembersHeader.vue'
);
const MemberRead = () => import(
  /* webpackChunkName: "member_admin" */
  '@/apps/members/MemberRead.vue'
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

const MemberStore = () => import(
  /* webpackChunkName: "member_admin" */
  '@/stores/members'
);

import makeStore from '@/js/makeVuex';
var store = makeStore();

export default [
  {
    path: '/members',
    component: App,
    async beforeEnter(to, from, next) {
      if (!to.meta.called) {
        to.meta.called = true;
        await store.setModule(['member'], MemberStore);
      }
      next();
    },
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
