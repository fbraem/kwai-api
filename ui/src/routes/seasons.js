export default [
    {
        path : '/seasons/:id(\\d+)',
        component : () => import(/* webpackChunkName: "seasons_chunck" */ '@/apps/seasons/app/SeasonRead.vue'),
        name : 'seasons.read',
    },
    {
        path : '/seasons/create',
        component :  () => import(/* webpackChunkName: "seasons_admin" */ '@/apps/seasons/app/SeasonForm.vue'),
        name : 'seasons.create'
    },
    {
        path : '/seasons/update/:id(\\d+)',
        component : () => import(/* webpackChunkName: "seasons_admin" */ '@/apps/seasons/app/SeasonForm.vue'),
        name : 'seasons.update',
    },
    {
        path : '/seasons',
        component : () => import(/* webpackChunkName: "seasons_chunck" */ '@/apps/seasons/app/SeasonBrowse.vue'),
        name : 'seasons.browse'
    }
];
