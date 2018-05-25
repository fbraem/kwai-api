import Model from '@/js/JSONAPI/BaseModel';

export default class Category extends Model {
    resourceName() {
        return 'categories';
    }

    fields() {
        return [
            'name',
            'description',
            'remark'
        ];
    }

    dates() {
        return {
            'created_at' : 'YYYY-MM-DD HH:mm:ss',
            'updated_at' : 'YYYY-MM-DD HH:mm:ss'
        }
    }
}
