<template>
  <div style="display:flex;flex-direction:column">
    <form :class="{ 'kwai-form-stacked': stacked }">
      <slot></slot>
    </form>
    <div
      v-if="hasSubmitListener"
      style="display: flex; justify-content: flex-end;"
    >
      <button
        class="kwai-button kwai-theme-primary"
        :disabled="!valid"
        @click.prevent.stop="submit"
      >
        <i :class="icon"></i>&nbsp; {{ save }}
      </button>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    /**
     * Is this form stacked?
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
      type: Array,
      default: () => []
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
    },
    icon: {
      type: String,
      default: 'fas fa-save'
    }
  },
  provide() {
    return {
      fields: this.form.fields
    };
  },
  created() {
    for (let f in this.form.fields) {
      this.$watch('form.fields.' + f + '.value', (nv, ov) => {
        this.form.validateField(f, true);
      });
    }
  },
  computed: {
    hasSubmitListener() {
      if (this.$listeners) {
        if (this.$listeners.submit) return true;
      }
      return false;
    },
    valid() {
      var valid = this.form.state.valid;

      if (valid) {
        this.form.validators.forEach((validator) => {
          if (!validator(this.form.fields)) {
            valid = false;
          }
        });
      }
      if (valid) {
        this.form.clearErrors();
      }
      return valid;
    }
  },
  watch: {
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
    },
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
            this.form.fields[attr].errors.push(item.title);
          }
        }
      });
    },
    validate() {
      var result = Object.entries(this.form.fields).every((entry) => {
        const [, field] = entry;
        return field.valid;
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
    },
    submit() {
      this.$emit('submit');
    }
  },
  mounted() {
    // Run all validations now to initialize
    this.form.validate(false);
  },
};
</script>
