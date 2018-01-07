import authPerimeter from './authPerimeter';
import categoryPerimeter from './categoryPerimeter';
import newsPerimeter from './newsPerimeter';

function perimeters() {
    return [
        authPerimeter,
        categoryPerimeter,
        newsPerimeter
    ];
};

export default perimeters;
