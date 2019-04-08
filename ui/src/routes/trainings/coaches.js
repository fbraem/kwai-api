import App from '@/site/App.vue';

import moment from 'moment';

const CoachesHeader = () =>
  import(/* webpackChunkName: "trainings_admin_chunck" */
    '@/apps/trainings/TheCoachesHeader.vue'
  );

const CoachHeader = () =>
  import(/* webpackChunkName: "trainings_admin_chunck" */
    '@/apps/trainings/TheCoachHeader.vue'
  );

const CoachBrowse = () =>
  import(/* webpackChunkName: "trainings_admin_chunck" */
    '@/apps/trainings/CoachBrowse.vue'
  );

const CoachRead = () =>
  import(/* webpackChunkName: "trainings_admin_chunck" */
    '@/apps/trainings/CoachRead.vue'
  );

const CoachTrainings = () =>
  import(/* webpackChunkName: "trainings_admin_chunck" */
    '@/apps/trainings/CoachTrainings.vue'
  );

const CoachForm = () =>
  import(/* webpackChunkName: "trainings_admin_chunck" */
    '@/apps/trainings/CoachForm.vue'
  );
const CoachFormHeader = () =>
  import(/* webpackChunkName: "trainings_admin_chunck" */
    '@/apps/trainings/TheCoachFormHeader.vue'
  );

const TrainingStore = () =>
  import(/* webpackChunckName: "trainings_store_chunck" */
    '@/stores/training'
  );
const CoachStore = () =>
  import(/* webpackChunckName: "trainings_store_chunck" */
    '@/stores/training/coaches'
  );
const MemberStore = () =>
  import(/* webpackChunckName: "trainings_store_chunck" */
    '@/stores/members'
  );

import makeStore from '@/js/makeVuex';
var store = makeStore();

export default [
  {
    path: '/trainings/coaches',
    component: App,
    async beforeEnter(to, from, next) {
      if (!to.meta.called) {
        to.meta.called = true;
        await store.setModule(['training'], TrainingStore);
        await store.setModule(['training', 'coach'], CoachStore);
      }
      next();
    },
    children: [
      {
        path: ':id(\\d+)',
        components: {
          header: CoachHeader,
          main: CoachRead
        },
        children: [
          {
            path: 'trainings/:year(\\d+)/:month(\\d+)',
            name: 'trainings.coaches.trainings',
            components: {
              coach_information: CoachTrainings
            },
            props: {
              coach_information: (route) => {
                return {
                  year: Number(route.params.year),
                  month: Number(route.params.month)
                };
              }
            }
          },
          {
            path: 'trainings',
            name: 'trainings.coaches.trainings.default',
            components: {
              coach_information: CoachTrainings
            },
            props: {
              coach_information: (route) => {
                return {
                  year: moment().year(),
                  month: moment().month() + 1
                };
              }
            }
          },
          {
            path: '',
            redirect: {
              name: 'trainings.coaches.trainings.default'
            },
            name: 'trainings.coaches.read'
          },
        ]
      },
      {
        path: 'create',
        async beforeEnter(to, from, next) {
          await store.setModule(['member'], MemberStore);
          next();
        },
        components: {
          header: CoachFormHeader,
          main: CoachForm
        },
        props: {
          header: {
            creating: true
          }
        },
        name: 'trainings.coaches.create',
      },
      {
        path: 'update/:id(\\d+)',
        async beforeEnter(to, from, next) {
          await store.setModule(['member'], MemberStore);
          next();
        },
        components: {
          header: CoachFormHeader,
          main: CoachForm
        },
        props: {
          header: {
            creating: false
          }
        },
        name: 'trainings.coaches.update',
      },
      {
        path: '',
        name: 'trainings.coaches',
        components: {
          header: CoachesHeader,
          main: CoachBrowse
        }
      },
    ]
  },
];
