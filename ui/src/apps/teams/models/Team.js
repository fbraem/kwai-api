import Model from '@/js/BaseModel';

import TeamType from './TeamType.js';
import Season from '@/apps/seasons/models/Season.js';

export default class Team extends Model {
    resourceName() {
        return 'teams';
    }

    fields() {
        return [
            'name'
        ];
    }

    relationships() {
        return {
            team_type : new TeamType(),
            season : new Season()
        };
    }
}
