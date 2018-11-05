import Model from './BaseModel';

export default class Country extends Model {
    resourceName() {
        return 'countries';
    }

    fields() {
        return [
            'name',
            'iso_2',
            'iso_3'
        ];
    }

    dates() {
        return {
            'created_at' : 'YYYY-MM-DD HH:mm:ss',
            'updated_at' : 'YYYY-MM-DD HH:mm:ss'
        }
    }
}
