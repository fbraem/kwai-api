import { validationMixin } from 'vuelidate';

/** Class representing Form data */
export default class Form {
  /**
   * Create a Form. Calls the static fields method to initialize all fields.
   */
  constructor() {
    this._fields = this.constructor.fields();
    this.reset();
  }

  /**
   * Overload this function and return an array with form objects
   * @return {array} An array with fields.
   */
  static fields() {
    return {};
  }

  validators() {
    var result = {};
    Object.entries(this._fields).forEach((entry) => {
      const [fieldName, field] = entry;
      if (!field.validators) {
        field.validators = {};
      }
      var validators = Array.isArray(field.validators)
        ? field.validators
        : [ field.validators ];
      var fieldValidator = {};
      validators.forEach((validator) => {
        fieldValidator = { ... fieldValidator, ... validator.v };
      });
      result[fieldName] = {
        value: {
          ... fieldValidator
        }
      };
    });
    return result;
  }

  /**
   * Resets all form fields
   */
  reset() {
    Object.entries(this._fields).forEach((entry) => {
      const [fieldName, field] = entry;
      this[fieldName] = {
        value: field.value,
        errors: []
      };
    });
  }

  /**
   * Clear all errors
   */
  clearErrors() {
    Object.keys(this._fields).forEach((fieldName) => {
      this[fieldName].errors = [];
    });
  }

  /**
   * Creates a computed value 'errors', which is an object containing all
   * form fields with error messages for validators that failed.
   */
  errorsMixin() {
    var form = this;

    var watches = {};
    Object.entries(form._fields).forEach((entry) => {
      const [fieldName, field] = entry;
      watches['$v.form.' + fieldName] = function(newV, oldV) {
        console.log('watch', fieldName);
        // const errors = [ ... form[fieldName].errors];
        const errors = [];
        if (newV) {
          var validators = Array.isArray(field.validators)
            ? field.validators
            : [ field.validators ];
          validators.every((val) => {
            var valid = true;
            Object.keys(val.v).forEach((validatorName) => {
              if (!this.$v.form[fieldName].value[validatorName]) {
                valid = false;
              }
            });
            if (!valid) {
              errors.push(this.$t(val.error));
            }
            return valid;
          });
        }
        this.$v.form[fieldName].$model.errors = errors;
      };
    });
    var mixin = {
      data: function() {
        return {
          form: form
        };
      },
      mixins: [validationMixin],
      validations() {
        return {
          form: form.validators()
        };
      },
      watch: watches
    };
    return mixin;
  }

};
