<template>
  <div class="kwai-addon">
    <div class="kwai-addon-item">
      <i class="fas fa-lock"></i>
    </div>
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
