<template>
  <input v-model="field.value.$model"
    :id="id"
    class="uk-input"
    :class="{ 'uk-form-danger' : field.value.$error }"
    type="text"
    v-bind="$attrs"
    />
</template>

<script>
export default {
  inject: {
    field: 'field',
    id: 'id'
  }
};
</script>
