import Model from './Model';
import { Attribute, DateAttribute } from './Attribute';

import moment from 'moment';

import Team from './Team';

/**
 * Season model
 */
export default class Season extends Model {
  static type() {
    return 'seasons';
  }

  static fields() {
    return {
      name: new Attribute(),
      remark: new Attribute(),
      start_date: new DateAttribute('YYYY-MM-DD'),
      end_date: new DateAttribute('YYYY-MM-DD'),
      created_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
      updated_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
    };
  }

  static relationships() {
    return {
      teams: Team
    };
  }

  static computed() {
    return {
      formatted_start_date(season) {
        if (season.start_date) {
          return season.start_date.format('L');
        }
        return '';
      },
      formatted_end_date(season) {
        if (season.end_date) {
          return season.end_date.format('L');
        }
        return '';
      },
      formatted_created_at(season) {
        if (season.created_at) {
          return season.created_at.format('L');
        }
        return '';
      },
      formatted_updated_at(season) {
        if (season.updated_at) {
          return season.updated_at.format('L');
        }
        return '';
      },
      active(season) {
        var today = moment();
        return today.isBetween(season.start_date, season.end_date)
                    || today.isSame(season.start_date)
                    || today.isSame(season.end_date);
      },
    };
  }
}
