import moment from 'moment';

import definitionsRouter from './definitions';
import coachesRouter from './coaches';

function routes() {
  var routes = [
    {
      path: '/trainings',
      redirect: {
        name: 'trainings.browse',
        params: {
          year: moment().year(),
          month: moment().month() + 1
        }
      }
    },
    {
      path: '/trainings/:id(\\d+)',
      component: () => import(/* webpackChunkName: "trainings_chunck" */
        '@/apps/trainings/TrainingRead.vue'),
      name: 'trainings.read'
    },
    {
      path: '/trainings/:year(\\d+)/:month(\\d+)',
      component: () => import(/* webpackChunkName: "trainings_chunck" */
        '@/apps/trainings/TrainingBrowse.vue'),
      name: 'trainings.browse',
      props: (route) => {
        const year = route.params.year
          ? Number(route.params.year) : moment().year();
        const month = route.params.month
          ? Number(route.params.month) : moment().month() + 1;
        return { year, month };
      }
    },
    {
      path: '/trainings/create',
      component: () => import(/* webpackChunkName: "trainings_admin_chunck" */
        '@/apps/trainings/TrainingForm.vue'),
      name: 'trainings.create',
    },
    {
      path: '/trainings/update/:id(\\d+)',
      component: () => import(/* webpackChunkName: "trainings_admin_chunck" */
        '@/apps/trainings/TrainingForm.vue'),
      name: 'trainings.update',
    },
  ];
  return routes.concat(definitionsRouter).concat(coachesRouter);
};

export default routes();
