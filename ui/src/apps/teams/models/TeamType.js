import Model from '@/js/JSONAPI/BaseModel';

export default class TeamType extends Model {
    resourceName() {
        return 'team_types';
    }

    fields() {
        return [
            'name',
            'start_age',
            'end_age',
            'competition',
            'gender',
            'active',
            'remark'
        ];
    }

    dates() {
        return {
            'created_at' : 'YYYY-MM-DD HH:mm:ss',
            'updated_at' : 'YYYY-MM-DD HH:mm:ss'
        }
    }

    relationships() {
        return {

        };
    }
}
