/**
 * A factory method for creating a form
 */
const Form = (fields, validators) => {
  if (typeof validators === 'undefined') validators = [];
  let state = {
    valid: true
  };

  const clearErrors = () => {
    Object.entries(fields).forEach((entry) => {
      const [, field] = entry;
      field.errors = field.errors.splice(0, field.errors.length);
    });
  };

  const validate = (showErrors) => {
    Object.keys(fields).forEach((name) => {
      validateField(name, showErrors);
    });
  };

  const validateField = (name, showErrors = true) => {
    var field = fields[name];
    field.errors.splice(0, field.errors.length);
    var valid = field.validators.every((validator) => {
      var vFn = validator.v.bind(field);
      field.valid = vFn(field.value, validator.params);
      if (!field.valid && showErrors) {
        field.errors.push(validator.error);
      }
      return field.valid;
    });

    if (valid) {
      state.valid = Object.entries(fields).every((entry) => {
        const [, field] = entry;
        return field.valid;
      });
    } else {
      state.valid = false;
    }

    return valid;
  };

  return { fields, state, validators, validate, validateField, clearErrors };
};

export default Form;


export const makeField = ({value, validators, required} = {}) => {
  if (typeof value === 'undefined') value = null;
  if (!validators) validators = [];
  if (typeof required === 'undefined') required = false;
  let errors = [];
  let valid = true;

  return {
    value,
    valid,
    errors,
    required,
    validators
  };
};

/**
 * Checks if a value is not empty
 */
export function notEmpty(value) {
  return value != null && value.length > 0;
}

/**
 * Checks if a value has a mininum length
 */
export function minLength(value, { min }) {
  return value != null && value.length >= min;
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
