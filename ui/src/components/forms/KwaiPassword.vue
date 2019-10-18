<template>
  <!-- eslint-disable max-len -->
  <div class="flex mb-4">
    <span class="flex items-center py-2 px-4 rounded rounded-r-none border border-r-0 bg-gray-500">
      <i class="fas fa-lock"></i>
    </span>
    <input v-model="field.value"
      :id="id"
      class="flex-grow flex-shrink h-auto appearance-once block w-full bg-gray-200 border py-3 px-4 leading-thight focus:outline-none focus:bg-white rounded rounded-l-none"
      :class="{ 'border-red-600' : field.errors.length > 0 }"
      type="password"
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
