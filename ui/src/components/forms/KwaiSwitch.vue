<template>
  <div>
    <label class="kwai-switch">
      <input v-model="field.value"
        :id="id"
        type="checkbox"
        :checked="isChecked"
        v-bind="$attrs"
      />
      <div class="kwai-switch-slider kwai-switch-on-off round"></div>
    </label>
  </div>
</template>

<script>
export default {
  inject: {
    field: 'field',
    id: 'id'
  },
  computed: {
    isChecked() {
      return this.field.value;
    }
  }
};
</script>
