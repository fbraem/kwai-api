import Model from './Model';
import { Attribute, DateAttribute } from './Attribute';

import User from './User';
import Category from './Category';
import Content from './Content';

/**
 * Event model
 */
export default class Event extends Model {
  static type() {
    return 'events';
  }

  // Event times are never stored in UTC. They are stored as localtime.
  static fields() {
    return {
      active: new Attribute(),
      start_date: new DateAttribute('YYYY-MM-DD HH:mm'),
      end_date: new DateAttribute('YYYY-MM-DD HH:mm'),
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
      category: Category,
      contents: Content,
      user: User,
    };
  }

  static computed() {
    return {
      formattedStartDate(event) {
        return event.start_date.format('L');
      },
      formattedStartTime(event) {
        return event.start_date.format('HH:mm');
      },
      formattedEndTime(event) {
        return event.end_date.format('HH:mm');
      }
    };
  }
}
