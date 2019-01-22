<template>
    <input v-model="field.value"
        :id="id"
        class="uk-checkbox"
        type="checkbox"
        :class="{ 'uk-form-danger' : field.errors.length > 0 }"
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
