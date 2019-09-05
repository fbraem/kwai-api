<template>
  <div style="display: flex; flex-direction: column; margin-bottom:20px;">
    <label
      v-if="hasLabel"
      class="kwai-form-label"
      :class="{ 'kwai-text-danger' : hasErrors }"
      :for="name"
    >
      {{ label }}
    </label>
    <slot></slot>
    <div
      v-if="hasErrors"
      class="kwai-text-danger"
    >
      <div
        v-for="(error, index) in fieldErrors"
        :key="index"
      >
        {{ error }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: [ 'name', 'label' ],
  inject: {
    fields: 'fields'
  },
  computed: {
    field() {
      return this.fields[this.name];
    },
    hasLabel() {
      return this.label && this.label.length > 0;
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
