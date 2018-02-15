import baseRouter from './base';
import newsRouter from './news';
import categoriesRouter from './categories';
import pagesRouter from './pages';
import usersRouter from './users';

function routes() {
    var routes = [];
    return routes.concat(baseRouter)
        .concat(newsRouter)
        .concat(categoriesRouter)
        .concat(pagesRouter)
        .concat(usersRouter)
};

export default routes;
