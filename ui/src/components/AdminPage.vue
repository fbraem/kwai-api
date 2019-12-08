<template>
  <div>
    <slot></slot>
    <div
      v-if="toolbar"
      class="p-3 flex flex-row justify-end"
    >
      <IconButtons
        :toolbar="toolbar"
        normalClass="text-gray-700"
        hoverClass="hover:bg-gray-300"
      />
    </div>
  </div>
</template>

<script>
import IconButtons from './IconButtons';

export default {
  components: {
    IconButtons
  },
  props: {
    toolbar: {
      type: Array,
      required: false
    }
  }
};
</script>
