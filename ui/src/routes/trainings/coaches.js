import moment from 'moment';

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
    component: CoachBrowse,
    name: 'trainings.coaches.browse',
  },
  {
    path: '/trainings/coaches/:id(\\d+)',
    component: CoachRead,
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
    path: '/trainings/coaches/create',
    component: CoachForm,
    name: 'trainings.coaches.create',
  },
  {
    path: '/trainings/coaches/update/:id(\\d+)',
    component: CoachForm,
    name: 'trainings.coaches.update',
  },
];
