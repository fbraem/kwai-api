import Model from '@/models/Model';
import { Attribute, DateAttribute } from '@/models/Attribute';
import Rule from './Rule';

/**
 * RuleGroup model
 */
export default class RuleGroup extends Model {
  static type() {
    return 'rule_groups';
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
