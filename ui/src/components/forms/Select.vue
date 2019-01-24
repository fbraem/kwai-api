<template>
  <div class="uk-form-controls">
    <select v-model="field.value"
        :id="id"
        class="uk-select"
        :class="{ 'uk-form-danger' : field.errors.length > 0 }"
        :required="field.required"
        v-bind="$attrs">
        <option value="" v-if="empty">{{ empty}}</option>
        <option v-for="(item, index) in items" :value="item.value" :key="index">
          {{ item.text }}
        </option>
    </select>
  </div>
</template>

<style>
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
