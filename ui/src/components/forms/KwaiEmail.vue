<template>
  <!-- eslint-disable max-len -->
  <KwaiInputGroupAddon left="far fa-envelope">
    <input v-model="field.value"
      :id="id"
      class="flex-shrink flex-grow flex-auto bg-gray-200 leading-normal border border-grey-light rounded rounded-l-none px-4 py-3 focus:border-blue focus:shadow"
      :class="{ 'border-red-600' : field.errors.length > 0 }"
      type="email"
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
