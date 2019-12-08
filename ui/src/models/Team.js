import Model from './Model';
import { Attribute, DateAttribute } from './Attribute';

import TeamCategory from './TeamCategory';
import Member from './Member';
import Season from './Season';

/**
 * Team model
 */
export default class Team extends Model {
  static type() {
    return 'teams';
  }

  static fields() {
    return {
      name: new Attribute(),
      active: new Attribute(),
      remark: new Attribute(),
      members_count: new Attribute(true),
      created_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
      updated_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
    };
  }

  static relationships() {
    return {
      team_category: TeamCategory,
      season: Season,
      members: Member,
    };
  }

/*
  async available(id) {
    var segments = this._uri.segment();
    segments.push(id);
    segments.push('available_members');
    this._uri.segment(segments);
    const config = {
      method: 'GET',
      url: this._uri.href(),
    };
    this.reset();
    var member = new Member();
    let response = await member.request(config);
    return member.respond(response);
  }

  async attach(id, members) {
    const requestConfig = {
      method: 'POST',
      url: `${this.resourceUrl()}/${id}/members`,
      data: {
        data: members.map((member) => {
          return member.serialize().data;
        }),
      },
    };
    let response = await this.request(requestConfig);
    var member = new Member();
    return member.respond(response);
  }

  async detach(id, members) {
    const requestConfig = {
      method: 'DELETE',
      url: `${this.resourceUrl()}/${id}/members`,
      data: {
        data: members.map((member) => {
          return member.serialize().data;
        }),
      },
    };
    let response = await this.request(requestConfig);
    var member = new Member();
    return member.respond(response);
  }
*/
}
