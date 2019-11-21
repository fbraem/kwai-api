import App from './App.vue';

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

export default [
  {
    path: '/trainings/coaches',
    component: App,
    children: [
      {
        path: ':id(\\d+)',
        components: {
          hero: CoachHeader,
          default: CoachRead
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
        components: {
          hero: CoachFormHeader,
          default: CoachForm
        },
        props: {
          hero: {
            creating: true
          }
        },
        name: 'trainings.coaches.create',
      },
      {
        path: 'update/:id(\\d+)',
        components: {
          hero: CoachFormHeader,
          default: CoachForm
        },
        props: {
          hero: {
            creating: false
          }
        },
        name: 'trainings.coaches.update',
      },
      {
        path: '',
        name: 'trainings.coaches',
        components: {
          hero: CoachesHeader,
          default: CoachBrowse
        }
      },
    ]
  },
];
