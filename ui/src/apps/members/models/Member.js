import Model from '@/js/JSONAPI/BaseModel';

import Person from '@/apps/persons/models/Person';

export default class Member extends Model {
    resourceName() {
        //TODO: sport undependent
        return 'members';
    }

    namespace() {
        return 'sport/judo';
    }

    fields() {
        return [
            'competition',
            'remark',
            'license'
        ];
    }

    dates() {
        return {
            'license_date' : 'YYYY-MM-DD',
            'created_at' : 'YYYY-MM-DD HH:mm:ss',
            'updated_at' : 'YYYY-MM-DD HH:mm:ss'
        }
    }

    relationships() {
        return {
            person : new Person()
        };
    }
}
