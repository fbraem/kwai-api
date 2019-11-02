import Model from './Model';
import { Attribute, DateAttribute } from './Attribute';

import Country from './Country';

/**
 * Country model
 */
export default class Contact extends Model {
  static type() {
    return 'contacts';
  }

  static fields() {
    return {
      address: new Attribute(),
      city: new Attribute(),
      county: new Attribute(),
      email: new Attribute(),
      mobile: new Attribute(),
      postal_code: new Attribute(),
      remark: new Attribute(),
      tel: new Attribute(),
      created_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
      updated_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
    };
  }

  static relationships() {
    return {
      country: Country,
    };
  }
}
