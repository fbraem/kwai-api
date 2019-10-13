<template>
    <input v-model="field.value"
        :id="id"
        type="checkbox"
        :class="{ 'kwai-form-danger' : field.errors.length > 0 }"
        v-bind="$attrs"
    />
</template>

<script>
export default {
  inject: {
    field: 'field',
    id: 'id'
  }
};
</script>
