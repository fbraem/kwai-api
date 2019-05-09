import Model from '@/models/Model';
import { Attribute, DateAttribute } from '@/models/Attribute';

/**
 * UserInvitation model
 */
export default class UserInvitation extends Model {
  static type() {
    return 'user_invitations';
  }

  static fields() {
    return {
      name: new Attribute(),
      expired_at: new DateAttribute('YYYY-MM-DD HH:mm:ss'),
      expired_at_timezone: new Attribute(),
      email: new Attribute(),
      remark: new Attribute(),
      created_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
      updated_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true)
    };
  }

  static computed() {
    return {
      isExpired(invitation) {
        var utc = invitation.expired_at.clone();
        utc.utcOffset('+00:00', true);
        return utc.local().isBefore();
      },
    };
  }

  async readByToken(token) {
    const requestConfig = {
      method: 'GET',
      url: `${this.resourceUrl()}/${token}`,
    };
    let response = await this.request(requestConfig);
    var invitation = new UserInvitation();
    return invitation.respond(response);
  }
}
