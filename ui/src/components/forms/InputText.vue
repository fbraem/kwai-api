<template>
  <input v-model="field.value"
    :id="id"
    class="uk-input"
    :class="{ 'uk-form-danger' : field.errors.length > 0 }"
    type="text"
    :required="field.required"
    :placeholder="placeholder"
    v-bind="$attrs"
    />
</template>

<style>
  input:required {
    border-left: 3px solid;
  }
</style>

<script>
export default {
  inject: {
    field: 'field',
    id: 'id'
  },
  computed: {
    placeholder() {
      if (this.field.placeholder) return this.field.placeholder;
      return '';
    }
  }
};
</script>
