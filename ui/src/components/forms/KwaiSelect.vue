<template>
  <!-- eslint-disable max-len -->
  <select
    v-model="field.value"
    :id="id"
    class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 leading-thight rounded focus:outline-none focus:shadow-outline focus:border-gray-500"
    :class="{ 'border-red-600' : field.errors.length > 0 }"
    :required="field.required"
    v-bind="$attrs">
    <option value="" v-if="empty">{{ empty}}</option>
    <option v-for="(item, index) in items" :value="item.value" :key="index">
      {{ item.text }}
    </option>
  </select>
</template>

<script>
export default {
  inject: {
    field: 'field',
    id: 'id'
  },
  props: [
    'empty',
    'items',
  ]
};
</script>
