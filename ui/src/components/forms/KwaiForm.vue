<template>
  <div class="uk-grid-small" uk-grid>
    <div class="uk-width-1-1">
      <form :class="{ 'uk-form-stacked': stacked }">
        <slot></slot>
      </form>
    </div>
    <div v-if="hasSubmitListener"
      class="uk-width-1-1"
    >
      <div uk-grid>
        <div class="uk-width-expand">
        </div>
        <div class="uk-width-auto">
          <button
            class="uk-button uk-button-primary"
            :disabled="!form.$valid"
            @click.prevent.stop="submit"
          >
            <i :class="icon"></i>&nbsp; {{ save }}
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
        this.form.fields[f].validate(true);
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
      var result = Object.entries(this.form.fields).every((entry) => {
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
    submit() {
      this.$emit('submit');
    }
  },
  mounted() {
    // Run all validations now to initialize
    for (var f in this.form.fields) {
      this.form.fields[f].validate(false);
    }
  }
};
</script>
