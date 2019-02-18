import App from '@/site/App.vue';

import moment from 'moment';

import definitionsRouter from './definitions';
import coachesRouter from './coaches';

const TrainingHeader = () =>
  import(/* webpackChunkName: "trainings_chunck" */
    '@/apps/trainings/TrainingHeader.vue'
  );
const TrainingRead = () =>
  import(/* webpackChunkName: "trainings_chunck" */
    '@/apps/trainings/TrainingRead.vue'
  );
const TrainingsHeader = () =>
  import(/* webpackChunkName: "trainings_chunck" */
    '@/apps/trainings/TrainingsHeader.vue'
  );
const TrainingBrowse = () =>
  import(/* webpackChunkName: "trainings_chunck" */
    '@/apps/trainings/TrainingBrowse.vue'
  );
const TrainingForm = () =>
  import(/* webpackChunkName: "trainings_chunck" */
    '@/apps/trainings/TrainingForm.vue'
  );
const TrainingFormHeader = () =>
  import(/* webpackChunkName: "trainings_chunck" */
    '@/apps/trainings/TrainingFormHeader.vue'
  );

const Store = () =>
  import('@/stores/training');
const CoachStore = () =>
  import('@/stores/training/coaches');
const SeasonStore = () =>
  import('@/stores/seasons');
const TeamStore = () =>
  import('@/stores/teams');

function routes() {
  var route = [
    {
      path: '/trainings',
      component: App,
      meta: {
        stores: [
          {
            ns: ['training'],
            create: Store
          },
        ]
      },
      children: [
        {
          path: ':id(\\d+)',
          components: {
            header: TrainingHeader,
            main: TrainingRead
          },
          name: 'trainings.read'
        },
        {
          path: ':year(\\d+)/:month(\\d+)',
          components: {
            header: TrainingsHeader,
            main: TrainingBrowse
          },
          name: 'trainings.browse',
          props: {
            main: (route) => {
              const year = route.params.year
                ? Number(route.params.year) : moment().year();
              const month = route.params.month
                ? Number(route.params.month) : moment().month() + 1;
              return { year, month };
            }
          }
        },
        {
          path: 'create',
          components: {
            header: TrainingFormHeader,
            main: TrainingForm
          },
          props: {
            header: {
              creating: true
            }
          },
          meta: {
            stores: [
              {
                ns: ['season'],
                create: SeasonStore
              },
              {
                ns: ['team'],
                create: TeamStore
              },
              {
                ns: ['training', 'coach'],
                create: CoachStore
              },
            ]
          },
          name: 'trainings.create',
        },
        {
          path: 'update/:id(\\d+)',
          components: {
            header: TrainingFormHeader,
            main: TrainingForm
          },
          props: {
            header: {
              creating: false
            }
          },
          meta: {
            stores: [
              {
                ns: ['season'],
                create: SeasonStore
              },
              {
                ns: ['team'],
                create: TeamStore
              },
            ]
          },
          name: 'trainings.update',
        },
        {
          path: '',
          redirect: {
            name: 'trainings.browse',
            params: {
              year: moment().year(),
              month: moment().month() + 1
            }
          }
        },
      ]
    },
  ];
  return route.concat(definitionsRouter).concat(coachesRouter);
};

export default routes();
