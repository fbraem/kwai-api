import Model from '../BaseModel';

import User from '../User';
import Member from '../Member';

export default class TrainingCoach extends Model {
  resourceName() {
    return 'coaches';
  }

  namespace() {
    return 'trainings';
  }

  fields() {
    return [
      'description',
      'diploma',
      'active',
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
      member: new Member(),
      user: new User(),
    };
  }

  computed() {
    return {
      name(coach) {
        if (coach.member && coach.member.person) {
          return coach.member.person.name;
        }
        return '';
      },
    };
  }
}
