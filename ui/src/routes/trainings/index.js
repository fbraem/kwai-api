import App from '@/site/App.vue';

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

import TrainingStore from '@/stores/training';
import CoachStore from '@/stores/training/coaches';
import SeasonStore from '@/stores/seasons';
import TeamStore from '@/stores/teams';
import NewsStore from '@/stores/news';
import CategoryStore from '@/stores/categories';
import PageStore from '@/stores/pages';
import MemberStore from '@/stores/members';

import makeStore from '@/js/makeVuex';
var store = makeStore();

const CATEGORY_APP = 'trainings';

var routes = [
  {
    path: '/trainings',
    component: App,
    beforeEnter(to, from, next) {
      store.setModule(['training'], TrainingStore);
      store.setModule(['category'], CategoryStore);
      next();
    },
    children: [
      {
        path: ':id(\\d+)',
        components: {
          header: TrainingHeader,
          main: TrainingRead
        },
        name: 'trainings.read',
      },
      {
        path: ':id(\\d+)/presences',
        components: {
          header: ThePresencesHeader,
          main: Presences
        },
        beforeEnter(to, from, next) {
          store.setModule(['team'], TeamStore);
          store.setModule(['member'], MemberStore);
          next();
        },
        name: 'trainings.presences',
      },
      {
        path: ':year(\\d+)/:month(\\d+)',
        components: {
          header: TrainingsHeader,
          main: TrainingBrowse
        },
        name: 'trainings.browse',
        meta: {
          app: CATEGORY_APP
        },
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
        beforeEnter(to, from, next) {
          store.setModule(['season'], SeasonStore);
          store.setModule(['team'], TeamStore);
          store.setModule(['training', 'coach'], CoachStore);
          next();
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
        beforeEnter(to, from, next) {
          store.setModule(['season'], SeasonStore);
          store.setModule(['team'], TeamStore);
          store.setModule(['training', 'coach'], CoachStore);
          next();
        },
        name: 'trainings.update',
      },
      {
        path: '',
        beforeEnter(to, from, next) {
          store.setModule(['category'], CategoryStore);
          store.setModule(['news'], NewsStore);
          store.setModule(['page'], PageStore);
          store.setModule(['training'], TrainingStore);
          store.setModule(['training', 'coach'], CoachStore);
          next();
        },
        meta: {
          app: CATEGORY_APP,
          image: require('@/apps/trainings/images/sport-3468115_1920.jpg')
        },
        components: {
          header: TrainingsHeader,
          main: TrainingIndex
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
