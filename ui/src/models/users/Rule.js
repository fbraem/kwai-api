import Model from '@/models/Model';
import { Attribute, DateAttribute } from '@/models/Attribute';
import RuleAction from './RuleAction';
import RuleSubject from './RuleSubject';

/**
 * Rule model
 */
export default class Rule extends Model {
  static type() {
    return 'rules';
  }

  static fields() {
    return {
      name: new Attribute(),
      remark: new Attribute(),
      owner: new Attribute(),
      created_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
      updated_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true)
    };
  }

  static relationships() {
    return {
      action: RuleAction,
      subject: RuleSubject,
    };
  }
}
