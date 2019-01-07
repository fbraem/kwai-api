import Model from '../Model';
import { Attribute, DateAttribute } from '../Attribute';

import Season from '../Season';
import User from '../User';
import Team from '../Team';

import moment from 'moment';

/**
 * TrainingDefinition model
 */
export default class TrainingDefinition extends Model {
  static type() {
    return 'definitions';
  }

  static namespaces() {
    return ['trainings'];
  }

  static fields() {
    return {
      name: new Attribute(),
      description: new Attribute(),
      weekday: new Attribute(),
      active: new Attribute(),
      location: new Attribute(),
      remark: new Attribute(),
      start_time: new DateAttribute('HH:mm'),
      end_time: new DateAttribute('HH:mm'),
      time_zone: new Attribute(),
      created_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
      updated_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
    };
  }

  static relationships() {
    return {
      season: Season,
      team: Team,
      user: User,
    };
  }

  static computed() {
    return {
      weekdayText(definition) {
        return moment.weekdays(true)[definition.weekday - 1];
      }
    };
  }
}
