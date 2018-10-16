import Model from './BaseModel';

export default class Category extends Model {
    resourceName() {
        return 'categories';
    }

    fields() {
        return [
            'name',
            'description',
            'remark',
            'images'
        ];
    }

    dates() {
        return {
            'created_at' : 'YYYY-MM-DD HH:mm:ss',
            'updated_at' : 'YYYY-MM-DD HH:mm:ss'
        }
    }

    computed() {
        return {
            header_picture(category) {
                if (category.images) {
                    return category.images.header;
                }
            },
            icon_picture(category) {
                if (category.images) {
                    return category.images.icon;
                }
            }
        }
    }
}
