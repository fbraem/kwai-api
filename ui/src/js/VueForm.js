/**
 * Our Vue Form plugin
 */
import Vue from 'vue';

export function notEmpty(value) {
  return value != null && value.length > 0;
}

import moment from 'moment';
export function isTime(value) {
  if (value) return moment(value, 'HH:mm', true).isValid();
  return true;
};
export function isDate(value) {
  if (value) return moment(value, 'L', true).isValid();
  return true;
};

export default {
  data() {
    var data = {};
    if (this.$options.form) {
      let fn = this.$options.form.bind(this);
      data.form = fn();
    }
    if (this.$options.validations) {
      let fn = this.$options.validations.bind(this);
      data.validations = fn();
    }
    return data;
  },
  provide() {
    return {
      form: this.form
    };
  },
  created() {
    if (this.$options.form) {
      Object.entries(this.form).forEach((entry) => {
        const [key, field] = entry;
        if (!key.startsWith('$')) {
          Vue.set(field, 'errors', []);
          Vue.set(field, 'valid', true);
          if (!field.validators) field.validators = [];
          if (field.validators.length > 0) {
            this.$watch('form.' + key + '.value', (nv, ov) => {
              field.errors = [];
              this.validateField(field, true);
            });
          }
          this.validateField(field);
        }
      });
    }
  },
  computed: {
    $valid() {
      var result = Object.entries(this.form).every((entry) => {
        const [key, field] = entry;
        if (!key.startsWith('$')) {
          return field.valid;
        }
        return true;
      });
      if (result) {
        if (this.validations) {
          this.validations.forEach((validator) => {
            validator.bind(this);
            if (!validator()) {
              result = false;
            }
          });
        }
      }
      if (result) {
        this.clearErrors();
      }
      return result;
    }
  },
  methods: {
    reset() {
      var fn = this.$options.form.bind(this);
      var form = fn();
      Object.entries(form).every((entry) => {
        const [key, field] = entry;
        this.form[key].value = field.value;
        return true;
      });
    },
    clearErrors() {
      Object.entries(this.form).forEach((entry) => {
        const [fieldName, field] = entry;
        if (!fieldName.startsWith('$')) {
          field.errors = [];
        }
      });
    },
    handleErrors(errors) {
      errors.forEach((item, index) => {
        if (item.source && item.source.pointer) {
          var attr = item.source.pointer.split('/').pop();
          if (this.form[attr]) {
            this.form[attr].errors.push(item.title);
          }
        }
      });
    },
    validateField(field, showErrors) {
      field.errors = [];
      field.valid = field.validators.every((validator) => {
        var validatorFn = validator.v;
        validatorFn.bind(this);
        if (!validatorFn(field.value)) {
          if (showErrors) field.errors.push(validator.error);
          return false;
        }
        return true;
      });
    }
  },
  mounted() {
    // Run all validations now to initialize
    if (this.$options.form) {
      Object.entries(this.form).forEach((entry) => {
        const [key, field] = entry;
        if (!key.startsWith('$')) { // Skip our own properties
          this.validateField(field, false);
        }
      });
    }
  }
};
