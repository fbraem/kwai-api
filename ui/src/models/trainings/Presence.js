import Model from '../Model';
import { Attribute, DateAttribute } from '../Attribute';

import User from '../User';
import Member from '../Member';
import Training from './Training';

/**
 * Presences model
 */
export default class Presence extends Model {
  static type() {
    return 'presences';
  }

  static namespace() {
    return ['trainings'];
  }

  static fields() {
    return {
      ... Member.fields(),
      presence_remark: new Attribute(),
      created_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
      updated_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
    };
  }

  static relationships() {
    return {
      ... Member.relationships(),
      user: User,
      training: Training
    };
  }
}
