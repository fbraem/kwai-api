import { validationMixin } from 'vuelidate';

/** Class representing Form data */
export default class Form {
  /**
   * Create a Form. Calls the static fields method to initialize all fields.
   */
  constructor() {
    this.$fields = this.constructor.fields();
    this.reset();
  }

  /**
   * Overload this function and return an array with form objects
   * @return {array} An array with fields.
   */
  static fields() {
    return {};
  }

  /**
   * Resets all form fields
   */
  reset() {
    Object.entries(this.$fields).forEach((entry) => {
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
    Object.entries(this.$fields).forEach((entry) => {
      const [fieldName ] = entry;
      this[fieldName].errors = [];
    });
  }

  /**
   * Handle errors from a JSONAPI request
   */
  handleErrors(errors) {
    errors.forEach((item, index) => {
      if (item.source && item.source.pointer) {
        var attr = item.source.pointer.split('/').pop();
        this[attr].errors.push(item.title);
      }
    });
  }

  /**
   * A mixin for:
   * + A computed value 'errors', which is an object containing all
   *   form fields with error messages for validators that failed.
   * + A form variable in data
   * + Integrates the vuelidate mixin
   * + Adds the validations method for vuelidate
   */
  mixin() {
    var form = this;

    return {
      data: function() {
        if (!form.$translated) {
          Object.entries(form.$fields).forEach((entry) => {
            const [, field] = entry;
            if (field.label && field.label.length > 0) {
              field.label = this.$t(field.label);
            }
          });
          form.$translated = true;
        }
        return {
          form: form
        };
      },
      mixins: [validationMixin],
      provide() {
        return {
          form: form,
          $v: this.$v,
          errors: () => this.errors
        };
      },
      computed: {
        errors: function() {
          const allErrors = {};
          Object.entries(form.$fields).forEach((entry) => {
            const [fieldName, field] = entry;

            const errors = [ ... form[fieldName].errors];
            allErrors[fieldName] = errors;

            if (!field.validators) return;

            if (this.$v.form[fieldName].$dirty) {
              var validators = Array.isArray(field.validators)
                ? field.validators
                : [ field.validators ];
              validators.every((val) => {
                var valid = true;
                if (val.v) {
                  Object.keys(val.v).forEach((validatorName) => {
                    if (!this.$v.form[fieldName].value[validatorName]) {
                      valid = false;
                    }
                  });
                }
                if (!valid) {
                  errors.push(this.$t(val.error));
                }
                return valid;
              });
            }
          });
          return allErrors;
        }
      },
      validations() {
        var result = {};
        Object.entries(form.$fields).forEach((entry) => {
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
        return {
          form: result
        };
      }
    };
  }

};
