import Model from '../Model';
import { Attribute, DateAttribute } from '../Attribute';

import User from '../User';
import Member from '../Member';

/**
 * Coaches model
 */
export default class Coach extends Model {
  static type() {
    return 'coaches';
  }

  static namespace() {
    return ['trainings'];
  }

  static fields() {
    return {
      description: new Attribute(),
      diploma: new Attribute(),
      active: new Attribute(),
      remark: new Attribute(),
      created_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
      updated_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
    };
  }

  static relationships() {
    return {
      member: Member,
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
