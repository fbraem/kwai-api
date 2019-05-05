/**
 * Base class for Model attributes
 */
class Attribute {
  /**
  * @param {boolean} readonly Is this attribute readonly?
  */
  constructor(readonly = false) {
    this.readonly = readonly;
  }

  /**
   * Called to store the attribute to a JSONAPI object. This implementation
   * just returns the argument as is.
   * @param {*} arg The value to convert
   * @return {*} The converted argument.
   */
  to(arg) { return arg; }

  /**
   * Called to retrieve the attribute from a JSONAPI object. This implementation
   * just returns the argument as is.
   * @param {*} arg The value to convert
   * @return {*} The converted argument.
   */
  from(arg) { return arg; }

  /**
   * Returns true when this is a readonly attribute. A readonly attribute will
   * never be stored in a JSONAPI structure
   * @return {boolean}
   */
  isReadonly() {
    return this.readonly;
  }
}

import moment from 'moment';

/**
 * DateAttribute is used to declare a date attribute.
 */
class DateAttribute extends Attribute {
  /**
  * @param {string} format The format used to store this date.
  * @param {boolean} readonly Is this attribute readonly?
  */
  constructor(format, readonly = false) {
    super(readonly);
    this.format = format;
  }

  /**
   * Returns a formatted date
   * @param {Moment} arg A date
   * @return {string}
   */
  to(arg) {
    if (arg) {
      return arg.format(this.format);
    }
    return null;
  }

  /**
   * Creates a Moment object with the format
   * @param {string} arg A date as a string
   * @return {moment}
   */
  from(arg) {
    if (arg) {
      return moment(arg, this.format);
    }
    return null;
  }
}

/**
 * ObjectAttribute is used for objects that are returned as JSON and not as
 * relationships
 */
class ObjectAttribute extends Attribute {
  /**
   * @param {object} fields An object with attributes
   * @param {boolean} readonly Is this attribute readonly?
   */
  constructor(fields, readonly = false) {
    super(readonly);
    this.fields = fields;
  }

  /**
   * Returns an object
   */
  to(arg) {
    var obj = Object.create(null);
    Object.entries(this.fields).forEach((entry) => {
      const [key, attr] = entry;
      if (!attr.isReadonly()) {
        if (key in arg) {
          if (arg[key] && attr instanceof Attribute) {
            obj[key] = attr.to(arg[key]);
          } else {
            obj[key] = arg[key];
          }
        }
      }
    });
    return obj;
  }

  /**
   * Returns a simple object
   * @param {object} arg A JSON object
   * @return {object}
   */
  from(arg) {
    var obj = Object.create(null);
    Object.entries(this.fields).forEach((entry) => {
      const [key, attr] = entry;
      if (attr instanceof Attribute) {
        obj[key] = attr.from(arg[key]);
      } else {
        obj[key] = arg[key];
      }
    });
    return obj;
  }
}

/**
 * ArrayAttribute is used for arrays that are returned in JSON
 */
class ArrayAttribute extends Attribute {
  /**
   * @param {Attribute} elementAttribute The atribute to use for the elements
   * of the array.
   * @param {boolean} readonly Is this attribute readonly?
   */
  constructor(elementAttribute, readonly = false) {
    super(readonly);
    this.elementAttribute = elementAttribute;
  }

  /**
   * Returns an array with values for a JSONAPI structure
   * @param {array} arg An array
   * @return {array}
   */
  to(arg) {
    if (Array.isArray(arg)) {
      return arg.map((e) => this.elementAttribute.to(e));
    }
    return arg;
  }

  /**
   * Returns an  array from the JSONAP structure
   * @param {array} arg An array
   * @return {array}
   */
  from(arg) {
    if (Array.isArray(arg)) {
      return arg.map((e) => this.elementAttribute.from(e));
    }
    return arg;
  }
}

export { Attribute, DateAttribute, ObjectAttribute, ArrayAttribute };
