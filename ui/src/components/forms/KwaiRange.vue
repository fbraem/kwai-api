<template>
  <input v-model="field.value"
    :id="id"
    class="kwai-range"
    :class="{ 'kwai-form-danger' : field.errors.length > 0 }"
    type="range"
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
