import Model from '../Model';
import { Attribute, DateAttribute } from '../Attribute';

import User from '@/models/users/User';
import Coach from './Coach';
import Training from './Training';

/**
 * Coaches model
 */
export default class TrainingCoach extends Model {
  static type() {
    return 'coaches';
  }

  static namespace() {
    return ['trainings'];
  }

  static fields() {
    return {
      ... Coach.fields(),
      present: new Attribute(),
      coach_type: new Attribute(),
      coach_remark: new Attribute(),
      payed: new Attribute(),
      remark: new Attribute(),
      created_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
      updated_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
    };
  }

  static relationships() {
    return {
      ... Coach.relationships(),
      training: Training,
      user: User,
    };
  }

  static computed() {
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
