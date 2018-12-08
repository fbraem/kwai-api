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
      if (!field.validators) return;
      var validators = Array.isArray(field.validators)
        ? field.validators
        : [ field.validators ];
      if (field.validators.length > 0) {
        var fieldValidator = {};
        validators.forEach((validator) => {
          fieldValidator = { ... fieldValidator, ... validator.v };
        });
        result[fieldName] = fieldValidator;
      }
    });
    return result;
  }

  /**
   * Resets all form fields
   */
  reset() {
    this._errors = {};
    Object.entries(this._fields).forEach((entry) => {
      const [fieldName, field] = entry;
      this[fieldName] = field.value;
      this._errors[fieldName] = [];
    });
  }

  /**
   * Clear all errors
   */
  clearErrors() {
    Object.keys(this._fields).forEach((fieldName) => {
      this._errors[fieldName] = [];
    });
  }

  /**
   * Creates a computed value 'errors', which is an object containing all
   * form fields with error messages for validators that failed.
   */
  errorsMixin() {
    var form = this;

    return {
      computed: {
        errors: function() {
          const allErrors = {};
          Object.entries(form._fields).forEach((entry) => {
            const [fieldName, field] = entry;

            const errors = [ ... form._errors[fieldName]];
            allErrors[fieldName] = errors;

            if (!field.validators) return;

            if (this.$v.form[fieldName].$dirty) {
              var validators = Array.isArray(field.validators)
                ? field.validators
                : [ field.validators ];
              validators.every((val) => {
                var valid = true;
                Object.keys(val.v).forEach((validatorName) => {
                  if (!this.$v.form[fieldName][validatorName]) valid = false;
                });
                if (!valid) {
                  errors.push(this.$t(val.error));
                }
                return valid;
              });
            }
          });
          return allErrors;
        }
      }
    };
  }

};
