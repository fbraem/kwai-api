<template>
  <select v-model="field.value.$model"
      :id="id"
      class="uk-select"
      :class="{ 'uk-form-danger' : field.value.$error }"
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
