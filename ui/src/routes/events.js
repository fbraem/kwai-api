import App from '@/site/App.vue';

const EventBrowse = () => import(/* webpackChunkName: "trainings_chunck" */
  '@/apps/events/EventBrowse.vue'
);
const EventRead = () => import(/* webpackChunkName: "trainings_chunck" */
  '@/apps/events/EventRead.vue'
);

import EventStore from '@/stores/events';

import makeStore from '@/js/makeVuex';
var store = makeStore();

export default [
  {
    path: '/events',
    component: App,
    beforeEnter(to, from, next) {
      store.setModule(['event'], EventStore);
      next();
    },
    children: [
      {
        path: '/events/:year(\\d+)/:month(\\d+)',
        components: {
          main: EventBrowse
        },
        name: 'events.browse',
        props: {
          main: (route) => {
            var result = {};
            if (route.params.year) result.year = Number(route.params.year);
            if (route.params.month) result.month = Number(route.params.month);
            return result;
          }
        }
      },
      {
        path: '/events/:id(\\d+)',
        components: {
          main: EventRead
        },
        name: 'events.read'
      },
      {
        path: '',
        components: {
          main: EventBrowse
        },
        name: 'events.home'
      },
    ]
  },
];
