<template>
  <div class="uk-margin">
    <input v-model="field.value"
      :id="id"
      class="uk-range"
      :class="{ 'uk-form-danger' : field.errors.length > 0 }"
      type="range"
      v-bind="$attrs"
    />
  </div>
</template>

<script>
export default {
  inject: {
    field: 'field',
    id: 'id'
  }
};
</script>
