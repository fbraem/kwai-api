import Model from './BaseModel';

import TeamType from './TeamType';
import Member from './Member';
import Season from './Season';

export default class Team extends Model {
  resourceName() {
    return 'teams';
  }

  fields() {
    return [
      'name',
      'active',
      'remark',
      'members_count',
    ];
  }

  dates() {
    return {
      created_at: 'YYYY-MM-DD HH:mm:ss',
      updated_at: 'YYYY-MM-DD HH:mm:ss',
    };
  }

  relationships() {
    return {
      team_type: new TeamType(),
      season: new Season(),
      members: new Member(),
    };
  }

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

}
