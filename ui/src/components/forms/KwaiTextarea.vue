<template>
  <div class="uk-form-controls">
    <textarea v-model="field.value"
        :id="id"
        class="uk-textarea"
        :class="{ 'uk-form-danger' : field.errors.length > 0 }"
        :required="field.required"
        v-bind="$attrs">
    </textarea>
  </div>
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
