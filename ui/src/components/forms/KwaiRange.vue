<template>
  <input v-model="field.value"
    :id="id"
    class="appearance-none w-full outline-none bg-gray-200"
    :class="{ 'border-red-600' : field.errors.length > 0 }"
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
