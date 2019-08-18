<template>
  <select v-model="field.value"
      :id="id"
      class="kwai-select"
      :class="{ 'kwai-form-danger' : field.errors.length > 0 }"
      :required="field.required"
      v-bind="$attrs">
      <option value="" v-if="empty">{{ empty}}</option>
      <option v-for="(item, index) in items" :value="item.value" :key="index">
        {{ item.text }}
      </option>
  </select>
</template>

<style scoped>
  select:required {
    border-left: 3px solid;
  }
</style>

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
