<template>
  <div class="uk-inline" style="width:100%">
    <span class="uk-form-icon" uk-icon="mail"></span>
    <input v-model="field.value"
      :id="id"
      class="uk-input"
      :class="{ 'uk-form-danger' : field.errors.length > 0 }"
      type="email"
      :required="field.required"
      v-bind="$attrs"
    />
  </div>
</template>

<style>
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
