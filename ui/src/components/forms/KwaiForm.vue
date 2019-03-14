<template>
  <div class="uk-grid-small" uk-grid>
    <div class="uk-width-1-1">
      <form class="{ 'uk-form-stacked': stacked }">
        <slot></slot>
      </form>
    </div>
    <div class="uk-width-1-1">
      <div uk-grid>
        <div class="uk-width-expand">
        </div>
        <div class="uk-width-auto">
          <button
            class="uk-button uk-button-primary"
            :disabled="!form.$valid"
            @click="submit"
          >
            <i class="fas fa-save"></i>&nbsp; {{ save }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    /**
     * Is this form stacked? The uk-form-stacked class will be applied
     */
    stacked: {
      type: Boolean,
      default: true
    },
    /**
     * The form
     */
    form: {
      type: Object,
      required: true
    },
    /**
     * Validations that depends on multiple fields
     */
    validations: {
      type: Array
    },
    /**
     * The error that can be returned by JSONAPI
     */
    error: {
      type: Error,
      required: false
    },
    /**
     * The label of the submit button
     */
    save: {
      type: String,
      default: 'Save'
    }
  },
  provide() {
    return {
      form: this.form
    };
  },
  created() {
    Object.entries(this.form).forEach((entry) => {
      const [key, field] = entry;
      if (!key.startsWith('$')) {
        this.$set(field, 'errors', []);
        this.$set(field, 'valid', true);
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
  },
  computed: {
    valid() {
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
        this.form.clearErrors();
      }
      return result;
    }
  },
  watch: {
    valid(nv, ov) {
      if (nv !== ov) {
        this.form.$valid = nv;
      }
    },
    error(nv) {
      if (nv) {
        if (nv.response.status === 422) {
          this.handleErrors(nv.response.data.errors);
        } else if (nv.response.status === 404){
          // this.error = err.response.statusText;
        } else {
          // TODO: check if we can get here ...
          console.log(nv);
        }
      }
    }
  },
  methods: {
    /**
     * Tries to set JSONAPI errors to the corresponding fields
     * @param {array} errors The JSONAPI errors
     */
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
    /**
     * Validates a field
     */
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
    },
    submit() {
      this.$emit('submit');
    }
  },
  mounted() {
    // Run all validations now to initialize
    Object.entries(this.form).forEach((entry) => {
      const [key, field] = entry;
      if (!key.startsWith('$')) {
        this.validateField(field, false);
      }
    });
  }
};
</script>
