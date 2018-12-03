import Model from '../BaseModel';

import Season from '../Season';
import User from '../User';

export default class TrainingDefinition extends Model {
  resourceName() {
    return 'definitions';
  }

  namespace() {
    return 'trainings';
  }

  fields() {
    return [
      'name',
      'description',
      'weekday',
      'start_time',
      'end_time',
      'active',
      'location',
      'remark',
    ];
  }

  dates() {
    return {
      created_at: 'YYYY-MM-DD HH:mm:ss',
      updated_at: 'YYYY-MM-DD HH:mm:ss',
    };
  }

  relationships() {
    return {
      season: new Season(),
      user: new User(),
    };
  }
}
