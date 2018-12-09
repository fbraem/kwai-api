<template>
  <textarea v-model="field.value.$model"
      :id="id"
      class="uk-textarea"
      :class="{ 'uk-form-danger' : field.value.$error }"
      v-bind="$attrs">
  </textarea>
</template>

<script>
export default {
  inject: {
    field: 'field',
    id: 'id'
  }
};
</script>
