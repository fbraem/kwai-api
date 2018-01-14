import basePerimeter from './basePerimeter';
import authPerimeter from './authPerimeter';
import categoryPerimeter from './categoryPerimeter';
import newsPerimeter from './newsPerimeter';

function perimeters() {
    return [
        basePerimeter,
        authPerimeter,
        categoryPerimeter,
        newsPerimeter
    ];
};

export default perimeters;
