import NewsApp from './App.vue';
import NewsCreate from './app/NewsCreate.vue';
import NewsUpdate from './app/NewsUpdate.vue';
import NewsBrowse from './app/NewsBrowse.vue';
import NewsRead from './app/NewsRead.vue';

export default [
        {
            path : '/',
            components : {
                default : NewsApp
            },
            children: [
                {
                    path : 'story/:id',
                    components : {
                        NewsContent : NewsRead
                    },
                    name : 'news.story',
                    props : {
                        NewsContent : true
                    }
                },
                {
                    path : 'category/:category_id',
                    components : {
                        NewsContent : NewsBrowse
                    },
                    name : 'news.category',
                    props : {
                        NewsContent : true
                    }
                },
                {
                    path : 'archive/:year/:month',
                    components : {
                        NewsContent : NewsBrowse
                    },
                    name : 'news.archive',
                    props : {
                        NewsContent : true
                    }
                },
                {
                    path : '',
                    components : {
                        NewsContent : NewsBrowse
                    },
                    name : 'news.browse'
                }
            ]
        },
        {
            path : '/create',
            component : NewsCreate,
            name : 'news.create'
        },
        {
            path : '/update/:id',
            component : NewsUpdate,
            name : 'news.update'
        }
];
