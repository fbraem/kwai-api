import Model from './Model';
import { Attribute, DateAttribute } from './Attribute';

import moment from 'moment';

import Country from './Country';
import Contact from './Contact';

/**
 * Person model
 */
export default class Person extends Model {
  static type() {
    return 'persons';
  }

  static fields() {
    return {
      lastname: new Attribute(),
      firstname: new Attribute(),
      gender: new Attribute(),
      birthdate: new DateAttribute('YYYY-MM-DD'),
      created_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
      updated_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true)
    };
  }

  static computed() {
    return {
      name(person) {
        return [person.lastname, person.firstname].filter(Boolean).join(' ');
      },
      age(person) {
        return moment().diff(person.birthdate, 'years');
      },
      formatted_birthdate(person) {
        if (person.birthdate) {
          return person.birthdate.format('L');
        }
        return '';
      },
      isMale(person) {
        return person.gender === 1;
      },
      isFemale(person) {
        return person.gender === 2;
      },
    };
  }

  static relationships() {
    return {
      nationality: Country,
      contact: Contact
    };
  }
}
