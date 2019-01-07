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
    return arg.format(this.format);
  }

  /**
   * Creates a Moment object with the format
   * @param {string} arg A date as a string
   * @return {moment}
   */
  from(arg) {
    return moment(arg, this.format);
  }
}

export { Attribute, DateAttribute };
