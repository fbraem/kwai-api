<template>
  <multiselect
    v-model="field.value"
    :id="id"
    placeholder=""
    v-bind="$attrs"
    :required="field.required"
  >
    <template
      v-if="field.placeholder"
      slot="placeholder"
    >
      {{ field.placeholder }}
    </template>
  </multiselect>
</template>

<style scoped>
  input:required {
    border-left: 3px solid;
  }
</style>

<script>
import Multiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.min.css';

export default {
  components: {
    Multiselect
  },
  inject: {
    field: 'field',
    id: 'id'
  }
};
</script>
