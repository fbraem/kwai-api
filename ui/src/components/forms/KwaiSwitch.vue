<template>
  <!-- eslint-disable max-len -->
  <div class="switch inline-block align-middle">
    <input v-model="field.value"
      :id="id"
      class="switch-checkbox"
      type="checkbox"
      :checked="isChecked"
      v-bind="$attrs"
    />
    <label class="switch-label" for="id"></label>
  </div>
</template>

<script>
export default {
  inject: {
    field: 'field',
    id: 'id'
  },
  computed: {
    isChecked() {
      return this.field.value;
    }
  }
};
</script>
