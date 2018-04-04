import basePerimeter from './basePerimeter';
import authPerimeter from './authPerimeter';
import categoryPerimeter from './categoryPerimeter';
import newsPerimeter from './newsPerimeter';
import memberPerimeter from './memberPerimeter';
import teamPerimeter from './teamPerimeter';

function perimeters() {
    return [
        basePerimeter,
        authPerimeter,
        categoryPerimeter,
        newsPerimeter,
        memberPerimeter,
        teamPerimeter
    ];
};

export default perimeters;
