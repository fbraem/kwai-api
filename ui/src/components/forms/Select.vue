<template>
  <select v-model="field.value"
      :id="id"
      class="uk-select"
      :class="{ 'uk-form-danger' : field.errors.length > 0 }"
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
