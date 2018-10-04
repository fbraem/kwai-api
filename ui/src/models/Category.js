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
}
