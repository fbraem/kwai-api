import baseRouter from './base';
import newsRouter from './news';
import categoriesRouter from './categories';
import pagesRouter from './pages';
import usersRouter from './users';
import membersRouter from './members';
import seasonsRouter from './seasons';
import teamsRouter from './teams';
import teamtypesRouter from './team_types';

function routes() {
    var routes = [];
    return routes.concat(baseRouter)
        .concat(newsRouter)
        .concat(categoriesRouter)
        .concat(pagesRouter)
        .concat(usersRouter)
        .concat(membersRouter)
        .concat(seasonsRouter)
        .concat(teamsRouter)
        .concat(teamtypesRouter)
};

export default routes;
