<template>
  <!-- eslint-disable max-len -->
  <textarea v-model="field.value"
    :id="id"
    class="appearance-once block w-full bg-gray-200 border rounded py-3 px-4 mb-3 leading-thight focus:outline-none focus:bg-white"
    :class="{ 'border-red-600' : field.errors.length > 0 }"
    :required="field.required"
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
