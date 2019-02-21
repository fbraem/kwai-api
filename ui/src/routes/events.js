import App from '@/site/App.vue';

const EventBrowse = () => import(/* webpackChunkName: "trainings_chunck" */
  '@/apps/events/EventBrowse.vue'
);
const EventRead = () => import(/* webpackChunkName: "trainings_chunck" */
  '@/apps/events/EventRead.vue'
);

const EventStore = () => import(/* webpackChunkName: "trainings_chunck" */
  '@/stores/events'
);

export default [
  {
    path: '/events',
    component: App,
    meta: {
      stores: [
        {
          ns: ['event'],
          create: EventStore
        },
      ]
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
