import Model from '../BaseModel';

import Season from '../Season';
import User from '../User';
import Team from '../Team';
import Definition from './Definition';
import Coach from './Coach';

export default class TrainingEvent extends Model {
  resourceName() {
    return 'events';
  }

  namespace() {
    return 'trainings';
  }

  fields() {
    return [
      'name',
      'description',
      'active',
      'time_zone',
      'cancelled',
      'location',
      'remark',
    ];
  }

  // Event times are never stored in UTC. They are stored as localtime.
  dates() {
    return {
      created_at: 'YYYY-MM-DD HH:mm:ss',
      updated_at: 'YYYY-MM-DD HH:mm:ss',
      start_date: 'YYYY-MM-DD',
      start_time: 'HH:mm',
      end_time: 'HH:mm'
    };
  }

  relationships() {
    return {
      season: new Season(),
      teams: new Team(),
      coaches: new Coach(),
      definition: new Definition(),
      user: new User(),
    };
  }
}
