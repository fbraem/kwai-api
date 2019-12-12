<template>
  <div class="flex flex-col">
    <label
      v-if="hasLabel"
      class="block font-bold mb-2"
      :class="{ 'text-red-600' : hasErrors }"
      :for="name"
    >
      {{ label }}
      <i
        v-if="field.required"
        class="fas fa-asterisk text-red-700 text-xs align-top">
      </i>
    </label>
    <slot></slot>
    <div
      v-for="(error, index) in fieldErrors"
      class="text-red-600 text-sm italic"
      :key="index"
    >
      {{ error }}
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
      return this.field.errors || [];
    },
    hasErrors() {
      return this.fieldErrors.length > 0;
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
