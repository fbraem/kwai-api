import Model from './Model';
import { Attribute, DateAttribute } from './Attribute';

import Person from './Person';

/**
 * Member model
 */
export default class Member extends Model {
  static type() {
    // TODO: sport undependent
    return 'members';
  }

  static namespace() {
    return ['sport', 'judo'];
  }

  static fields() {
    return {
      competition: new Attribute(),
      remark: new Attribute(),
      license: new Attribute(),
      active: new Attribute(),
      license_date: new DateAttribute('YYYY-MM-DD'),
      created_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
      updated_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true)
    };
  }

  static relationships() {
    return {
      person: Person,
    };
  }
}
