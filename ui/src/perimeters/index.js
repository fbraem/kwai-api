import basePerimeter from './basePerimeter';
import authPerimeter from './authPerimeter';
import categoryPerimeter from './categoryPerimeter';
import newsPerimeter from './newsPerimeter';
import pagePerimeter from './pagePerimeter';
import memberPerimeter from './memberPerimeter';
import teamPerimeter from './teamPerimeter';
import userPerimeter from './userPerimeter';

function perimeters() {
    return [
        basePerimeter,
        authPerimeter,
        categoryPerimeter,
        newsPerimeter,
        pagePerimeter,
        memberPerimeter,
        teamPerimeter,
        userPerimeter
    ];
};

export default perimeters;
