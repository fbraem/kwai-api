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
    form: 'form'
  },
  computed: {
    field() {
      return this.form[this.name];
    },
    hasLabel() {
      return this.field.label && this.field.label.length > 0;
    },
    label() {
      return this.field.label;
    },
    fieldErrors() {
      return this.field.errors;
    },
    hasErrors() {
      return this.fieldErrors && this.fieldErrors.length > 0;
    }
  },
  provide() {
    return {
      field: this.field,
      id: this.name
    };
  }
};
</script>
