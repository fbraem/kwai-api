import Model from '@/js/BaseModel';

export default class TeamType extends Model {
    resourceName() {
        return 'team_types';
    }

    fields() {
        return [
            'name'
        ];
    }

    relationships() {
        return {

        };
    }
}
