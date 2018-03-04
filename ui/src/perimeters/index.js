import basePerimeter from './basePerimeter';
import authPerimeter from './authPerimeter';
import categoryPerimeter from './categoryPerimeter';
import newsPerimeter from './newsPerimeter';
import memberPerimeter from './memberPerimeter';

function perimeters() {
    return [
        basePerimeter,
        authPerimeter,
        categoryPerimeter,
        newsPerimeter,
        memberPerimeter
    ];
};

export default perimeters;
