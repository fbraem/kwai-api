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

import TrainingStore from '@/stores/training';
import CoachStore from '@/stores/training/coaches';
import MemberStore from '@/stores/members';

import makeStore from '@/js/makeVuex';
var store = makeStore();

export default [
  {
    path: '/trainings/coaches',
    component: App,
    beforeEnter(to, from, next) {
      store.setModule(['training'], TrainingStore);
      store.setModule(['training', 'coach'], CoachStore);
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
        beforeEnter(to, from, next) {
          store.setModule(['member'], MemberStore);
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
        beforeEnter(to, from, next) {
          store.setModule(['member'], MemberStore);
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
