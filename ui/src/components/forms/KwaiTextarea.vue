<template>
  <textarea v-model="field.value"
      :id="id"
      class="kwai-textarea"
      :class="{ 'kwai-form-danger' : field.errors.length > 0 }"
      :required="field.required"
      v-bind="$attrs">
  </textarea>
</template>

<style scoped>
  textarea:required {
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
