<template>
  <div class="uk-form-controls">
    <div class="uk-inline" style="width:100%">
      <span class="uk-form-icon" uk-icon="lock"></span>
      <input v-model="field.value"
        :id="id"
        class="uk-input"
        :class="{ 'uk-form-danger' : field.errors.length > 0 }"
        type="password"
        :required="field.required"
        v-bind="$attrs"
      />
    </div>
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
