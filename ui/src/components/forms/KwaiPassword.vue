<template>
  <div class="kwai-addon">
    <i class="kwai-addon-item fas fa-lock"></i>
    <input v-model="field.value"
      :id="id"
      class="kwai-input kwai-addon-field"
      :class="{ 'kwai-form-danger' : field.errors.length > 0 }"
      type="password"
      :required="field.required"
      v-bind="$attrs"
    />
  </div>
</template>

<style scoped>
  input:required {
    border-left: 3px solid;
  }
</style>

<script>
export default {
  inject: {
    field: 'field',
    id: 'id'
  }
};
</script>
