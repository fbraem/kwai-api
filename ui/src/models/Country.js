import Model from './Model';
import { Attribute, DateAttribute } from './Attribute';

/**
 * Country model
 */
export default class Country extends Model {
  static type() {
    return 'countries';
  }

  static fields() {
    return {
      name: new Attribute(),
      iso_2: new Attribute(),
      iso_3: new Attribute(),
      created_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
      updated_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
    };
  }
}
