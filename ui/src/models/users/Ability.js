import Model from '@/models/Model';
import { Attribute, DateAttribute } from '@/models/Attribute';
import Rule from './Rule';

/**
 * Ability model
 */
export default class Ability extends Model {
  static type() {
    return 'abilities';
  }

  static namespace() {
    return ['users'];
  }

  static fields() {
    return {
      name: new Attribute(),
      remark: new Attribute(),
      created_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
      updated_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true)
    };
  }

  static relationships() {
    return {
      rules: Rule
    };
  }
}
