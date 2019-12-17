<template>
  <!-- eslint-disable max-len -->
  <KwaiInputGroupAddon left="fas fa-lock">
    <input v-model="field.value"
      :id="id"
      class="flex-grow flex-shrink appearance-once bg-gray-200 border py-3 px-4 leading-normal focus:outline-none focus:shadow-outline rounded rounded-l-none"
      :class="{ 'border-red-600' : field.errors.length > 0 }"
      type="password"
      :required="field.required"
      v-bind="$attrs"
    />
  </KwaiInputGroupAddon>
</template>

<script>
import KwaiInputGroupAddon from './KwaiInputGroupAddon';

export default {
  components: {
    KwaiInputGroupAddon
  },
  inject: {
    field: 'field',
    id: 'id'
  }
};
</script>
