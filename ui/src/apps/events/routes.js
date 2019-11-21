import App from './App';

const EventBrowse = () => import(/* webpackChunkName: "trainings_chunck" */
  '@/apps/events/EventBrowse'
);
const EventRead = () => import(/* webpackChunkName: "trainings_chunck" */
  '@/apps/events/EventRead'
);

export default [
  {
    path: '/events',
    component: App,
    children: [
      {
        path: '/events/:year(\\d+)/:month(\\d+)',
        components: {
          default: EventBrowse
        },
        name: 'events.browse',
        props: {
          default: (route) => {
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
          default: EventRead
        },
        name: 'events.read'
      },
      {
        path: '',
        components: {
          default: EventBrowse
        },
        name: 'events.home'
      },
    ]
  },
];
