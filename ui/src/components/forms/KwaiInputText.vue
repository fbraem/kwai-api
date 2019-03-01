<template>
  <div class="uk-form-controls">
    <input v-model="field.value"
      :id="id"
      class="uk-input"
      :class="{ 'uk-form-danger' : field.errors.length > 0 }"
      type="text"
      :required="field.required"
      v-bind="$attrs"
      />
  </div>
</template>

<style scoped>
  input:required {
    border-left: 3px solid;
  }
</style>

<script>
export default {
  inject: {
    field: 'field',
    id: 'id'
  }
};
</script>
