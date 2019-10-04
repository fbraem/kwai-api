<template>
  <!-- eslint-disable max-len -->
  <div class="flex mb-6 border rounded">
    <div class="bg-gray-500">
      <i class="far fa-envelope"></i>
    </div>
    <input v-model="field.value"
      :id="id"
      class="flex h-auto appearance-once block w-full bg-gray-200 border py-3 px-4 mb-3 leading-thight focus:outline-none focus:bg-white"
      :class="{ 'border-red-600' : field.errors.length > 0 }"
      type="email"
      :required="field.required"
      v-bind="$attrs"
    />
  </div>
</template>

<style scoped>
input:required {
  @apply border-l-4 border-solid;
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
