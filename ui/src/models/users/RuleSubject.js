import Model from '@/models/Model';
import { Attribute, DateAttribute } from '@/models/Attribute';

/**
 * RuleSubject model
 */
export default class RuleSubject extends Model {
  static type() {
    return 'rule_subjects';
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
