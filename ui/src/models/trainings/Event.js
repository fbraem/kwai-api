import Model from '../Model';
import { Attribute, DateAttribute } from '../Attribute';

import Season from '../Season';
import User from '../User';
import Team from '../Team';
import Definition from './Definition';
import Coach from './Coach';

/**
 * Event model
 */
export default class TrainingEvent extends Model {
  static type() {
    return 'events';
  }

  static namespace() {
    return ['trainings'];
  }

  // Event times are never stored in UTC. They are stored as localtime.
  static fields() {
    return {
      name: new Attribute(),
      description: new Attribute(),
      active: new Attribute(),
      start_date: new DateAttribute('YYYY-MM-DD'),
      start_time: new DateAttribute('HH:mm'),
      end_time: new DateAttribute('HH:mm'),
      time_zone: new Attribute(),
      cancelled: new Attribute(),
      location: new Attribute(),
      remark: new Attribute(),
      created_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
      updated_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
    };
  }

  static relationships() {
    return {
      season: Season,
      teams: Team,
      coaches: Coach,
      definition: Definition,
      user: User,
    };
  }

  static computed() {
    return {
      formattedStartDate(event) {
        return event.start_date.format('L');
      },
      formattedStartTime(event) {
        return event.start_time.format('HH:mm');
      },
      formattedEndTime(event) {
        return event.end_time.format('HH:mm');
      }
    };
  }
}
