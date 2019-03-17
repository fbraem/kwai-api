/**
 * A factory method for creating a form
 */
const Form = (fields) => {
  const clearErrors = () => {
    Object.entries(fields).forEach((entry) => {
      const [, field] = entry;
      field.errors = field.errors.splice(0, field.errors.length);
    });
  };
  const $valid = false;
  return { fields, $valid, clearErrors };
};
export default Form;


export const makeField = ({value, validators, required} = {}) => {
  if (typeof value === undefined) value = null;
  if (!validators) validators = [];
  if (typeof required === undefined) required = false;
  let errors = [];
  let valid = true;

  return {
    value,
    valid,
    errors,
    required,
    validators,
    validate(showErrors) {
      this.errors.splice(0, this.errors.length);
      this.valid = validators.every((validator) => {
        var validatorFn = validator.v;
        if (!validatorFn(this.value)) {
          if (showErrors) this.errors.push(validator.error);
          return false;
        }
        return true;
      });
    }
  };
};

/**
 * Checks if a value is not empty
 */
export function notEmpty(value) {
  return value != null && value.length > 0;
}

import moment from 'moment';

/**
 * Checks a time with format HH:mm
 */
export function isTime(value) {
  if (value) return moment(value, 'HH:mm', true).isValid();
  return true;
};

/**
 * Check the date with format 'L'
 */
export function isDate(value) {
  if (value) return moment(value, 'L', true).isValid();
  return true;
};

/**
 * Check if the value is an integer
 */
export function isInteger(value) {
  if (value) return /^(0|[1-9]\d*)$/.test(value);
  return true;
};

/**
 * A simple function to validate email.
 */
export function isEmail(value) {
  if (value) {
    var re = /^\S+@\S+$/;
    return value.match(re);
  }
  return true;
};
