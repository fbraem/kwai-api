import App from './App.vue';

import moment from 'moment';

import definitionsRouter from './definitions';
import coachesRouter from './coaches';

const TrainingHeader = () =>
  import(/* webpackChunkName: "trainings_chunck" */
    '@/apps/trainings/TheTrainingHeader.vue'
  );
const TrainingRead = () =>
  import(/* webpackChunkName: "trainings_chunck" */
    '@/apps/trainings/TrainingRead.vue'
  );
const TrainingsHeader = () =>
  import(/* webpackChunkName: "trainings_chunck" */
    '@/apps/trainings/TheTrainingsHeader.vue'
  );
const TrainingBrowse = () =>
  import(/* webpackChunkName: "trainings_chunck" */
    '@/apps/trainings/TrainingBrowse.vue'
  );
const TrainingIndex = () =>
  import(/* webpackChunkName: "trainings_chunck" */
    '@/apps/trainings/Index.vue'
  );
const TrainingForm = () =>
  import(/* webpackChunkName: "trainings_admin_chunck" */
    '@/apps/trainings/TrainingForm.vue'
  );
const TrainingFormHeader = () =>
  import(/* webpackChunkName: "trainings_admin_chunck" */
    '@/apps/trainings/TheTrainingFormHeader.vue'
  );
const Presences = () =>
  import(/* webpackChunkName: "trainings_chunck" */
    '@/apps/trainings/Presences.vue'
  );
const ThePresencesHeader = () =>
  import(/* webpackChunkName: "trainings_chunck" */
    '@/apps/trainings/ThePresencesHeader.vue'
  );

const CATEGORY_APP = 'trainings';

var routes = [
  {
    path: '/trainings',
    component: App,
    children: [
      {
        path: ':id(\\d+)',
        components: {
          hero: TrainingHeader,
          default: TrainingRead
        },
        name: 'trainings.read',
      },
      {
        path: ':id(\\d+)/presences',
        components: {
          hero: ThePresencesHeader,
          default: Presences
        },
        name: 'trainings.presences',
      },
      {
        path: ':year(\\d+)/:month(\\d+)',
        components: {
          hero: TrainingsHeader,
          default: TrainingBrowse
        },
        name: 'trainings.browse',
        meta: {
          app: CATEGORY_APP
        },
        props: {
          default: (route) => {
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
          hero: TrainingFormHeader,
          default: TrainingForm
        },
        props: {
          hero: {
            creating: true
          }
        },
        name: 'trainings.create',
      },
      {
        path: 'update/:id(\\d+)',
        components: {
          hero: TrainingFormHeader,
          default: TrainingForm
        },
        props: {
          hero: {
            creating: false
          }
        },
        name: 'trainings.update',
      },
      {
        path: '',
        meta: {
          app: CATEGORY_APP,
          image: require('@/apps/trainings/images/sport-3468115_1920.jpg')
        },
        components: {
          hero: TrainingsHeader,
          default: TrainingIndex
        },
        name: 'trainings.index'
      },
    ]
  },
];

routes = routes.concat(definitionsRouter);
routes = routes.concat(coachesRouter);

for (let route of routes) {
  let meta = route.meta || {};
  meta.app = CATEGORY_APP;
  route.meta = meta;
}

export default routes;
