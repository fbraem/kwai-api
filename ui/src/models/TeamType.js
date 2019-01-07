import Model from './Model';
import { Attribute, DateAttribute } from './Attribute';

/**
 * TeamType model
 */
export default class TeamType extends Model {
  static type() {
    return 'team_types';
  }

  static fields() {
    return {
      name: new Attribute(),
      start_age: new Attribute(),
      end_age: new Attribute(),
      competition: new Attribute,
      gender: new Attribute(),
      active: new Attribute(),
      remark: new Attribute(),
      created_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
      updated_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
    };
  }
}
