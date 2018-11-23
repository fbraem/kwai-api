import Model from './BaseModel';

export default class UserInvitation extends Model {
  resourceName() {
    return 'user_invitations';
  }

  fields() {
    return [
      'name',
      'expired_at_timezone',
      'email',
      'remark',
    ];
  }

  dates() {
    return {
      expired_at: 'YYYY-MM-DD HH:mm:ss',
      created_at: 'YYYY-MM-DD HH:mm:ss',
      updated_at: 'YYYY-MM-DD HH:mm:ss',
    };
  }

  computed() {
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
