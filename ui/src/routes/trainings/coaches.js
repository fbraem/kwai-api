import moment from 'moment';

const Coaches = () =>
import(/* webpackChunkName: "trainings_admin_chunck" */
  '@/apps/trainings/Coaches.vue');

const CoachesHeader = () =>
import(/* webpackChunkName: "trainings_admin_chunck" */
  '@/apps/trainings/CoachesHeader.vue');

const CoachHeader = () =>
  import(/* webpackChunkName: "trainings_admin_chunck" */
    '@/apps/trainings/CoachHeader.vue');

const CoachBrowse = () =>
  import(/* webpackChunkName: "trainings_admin_chunck" */
    '@/apps/trainings/CoachBrowse.vue');

const CoachRead = () =>
  import(/* webpackChunkName: "trainings_admin_chunck" */
    '@/apps/trainings/CoachRead.vue');

const CoachTrainings = () =>
  import(/* webpackChunkName: "trainings_admin_chunck" */
    '@/apps/trainings/CoachTrainings.vue');

const CoachForm = () =>
  import(/* webpackChunkName: "trainings_admin_chunck" */
    '@/apps/trainings/CoachForm.vue');

export default [
  {
    path: '/trainings/coaches',
    component: Coaches,
    children: [
      {
        path: ':id(\\d+)',
        components: {
          coach_header: CoachHeader,
          coach_main: CoachRead
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
        component: CoachForm,
        name: 'trainings.coaches.create',
      },
      {
        path: 'update/:id(\\d+)',
        component: CoachForm,
        name: 'trainings.coaches.update',
      },
      {
        path: '',
        name: 'trainings.coaches',
        components: {
          coach_header: CoachesHeader,
          coach_main: CoachBrowse
        }
      },
    ]
  },
];
