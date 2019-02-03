import Model from '../Model';
import { DateAttribute } from '../Attribute';

import Season from '../Season';
import User from '../User';
import Team from '../Team';
import Definition from './Definition';
import Coach from './Coach';
import Event from '@/models/Event';

/**
 * Event model
 */
export default class Training extends Model {
  static type() {
    return 'trainings';
  }

  static fields() {
    return {
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
      event: Event,
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
