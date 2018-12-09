<template>
  <!-- eslint-disable max-len -->
  <div class="uk-margin">
    <label v-if="hasLabel" class="uk-form-label uk-text-bold" :class="{ 'uk-text-danger' : hasErrors }" :for="name">
      {{ label }}
    </label>
    <slot></slot>
    <div v-if="hasErrors" class="uk-text-danger uk-margin-small">
      <div v-for="(error, index) in fieldErrors" :key="index">
        {{ error }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: [ 'name' ],
  inject: {
    form: 'form',
    $v: '$v',
    errors: 'errors'
  },
  computed: {
    hasLabel() {
      return this.form.$fields[this.name].label
        && this.form.$fields[this.name].label.length > 0;
    },
    label() {
      return this.form.$fields[this.name].label;
    },
    field() {
      return this.form[this.name];
    },
    fieldValidator() {
      return this.$v.form[this.name];
    },
    fieldErrors() {
      return this.errors()[this.name];
    },
    hasErrors() {
      return this.fieldErrors && this.fieldErrors.length > 0;
    }
  },
  provide() {
    return {
      field: this.fieldValidator,
      id: this.name
    };
  }
};
</script>
