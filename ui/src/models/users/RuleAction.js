import Model from '@/models/Model';
import { Attribute, DateAttribute } from '@/models/Attribute';

/**
 * RuleAction model
 */
export default class RuleAction extends Model {
  static type() {
    return 'rule_actions';
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
}
