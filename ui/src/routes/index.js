import baseRouter from './base';
import newsRouter from './news';
import categoriesRouter from './categories';
import pagesRouter from './pages';
import usersRouter from './users';
import membersRouter from './members';

function routes() {
    var routes = [];
    return routes.concat(baseRouter)
        .concat(newsRouter)
        .concat(categoriesRouter)
        .concat(pagesRouter)
        .concat(usersRouter)
        .concat(membersRouter)
};

export default routes;
