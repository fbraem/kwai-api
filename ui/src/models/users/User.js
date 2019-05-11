import Model from '@/models/Model';
import { Attribute, DateAttribute } from '@/models/Attribute';
import RuleGroup from './RuleGroup';

/**
 * User model
 */
export default class User extends Model {
  static type() {
    return 'users';
  }

  static fields() {
    return {
      first_name: new Attribute(),
      last_name: new Attribute(),
      password: new Attribute(),
      email: new Attribute(),
      remark: new Attribute(),
      last_login: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
      created_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
      updated_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true)
    };
  }

  static relationships() {
    return {
      rule_groups: RuleGroup
    };
  }

  static computed() {
    return {
      name(user) {
        return [user.last_name, user.first_name].filter(Boolean).join(' ');
      },
      lastLoginFormatted(story) {
        if (story.last_login) {
          return story.last_login.format('L HH:mm');
        }
        return '';
      },
      createdAtFormatted(story) {
        if (story.created_at) {
          return story.created_at.format('L HH:mm');
        }
        return '';
      },
    };
  }

  async createWithToken(token) {
    var data = this.serialize();
    const requestConfig = {
      method: 'POST',
      url: `${this.resourceUrl()}/${token}`,
      data: data,
    };
    let response = await this.request(requestConfig);
    return this.respond(response);
  }
}
