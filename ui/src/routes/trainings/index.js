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

const Store = () =>
  import(
    /* webpackChunkName: "trainings_chunck" */
    '@/stores/training'
  );
const CoachStore = () =>
  import(
    /* webpackChunkName: "trainings_chunck" */
    '@/stores/training/coaches'
  );
const SeasonStore = () =>
  import(
    /* webpackChunkName: "seasons_chunck" */
    '@/stores/seasons'
  );
const TeamStore = () =>
  import(
    /* webpackChunkName: "teams_chunck" */
    '@/stores/teams'
  );
const NewsStore = () =>
  import(
    /* webpackChunkName: "news_chunck" */
    '@/stores/news'
  );
const CategoryStore = () =>
  import(
    /* webpackChunkName: "categories_chunck" */
    '@/stores/categories'
  );
const PageStore = () =>
  import(
    /* webpackChunkName: "pages_chunck" */
    '@/stores/pages'
  );

import makeStore from '@/js/makeVuex';
var store = makeStore();

const CATEGORY_APP = 'trainings';

function routes() {
  var route = [
    {
      path: '/trainings',
      component: App,
      async beforeEnter(to, from, next) {
        if (!to.meta.called) {
          to.meta.called = true;
          await store.setModule(['training'], Store);
          await store.setModule(['category'], CategoryStore);
        }
        to.meta.app = CATEGORY_APP;
        await store.dispatch('category/readApp', {
          app: to.meta.app
        });
        next();
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
          async beforeEnter(to, from, next) {
            await store.setModule(['season'], SeasonStore);
            await store.setModule(['team'], TeamStore);
            await store.setModule(['training', 'coach'], CoachStore);
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
          async beforeEnter(to, from, next) {
            await store.setModule(['season'], SeasonStore);
            await store.setModule(['team'], TeamStore);
            await store.setModule(['training', 'coach'], CoachStore);
            next();
          },
          name: 'trainings.update',
        },
        {
          path: '',
          async beforeEnter(to, from, next) {
            await store.setModule(['news'], NewsStore);
            await store.setModule(['page'], PageStore);
            await store.setModule(['training', 'coach'], CoachStore);
            next();
          },
          meta: {
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
  return route.concat(definitionsRouter).concat(coachesRouter);
};

export default routes();
