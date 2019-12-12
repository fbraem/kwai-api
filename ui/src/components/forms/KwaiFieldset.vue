<template functional>
  <div class="flex flex-wrap border-t py-4">
    <div class="w-full sm:w-1/3 mb-3">
      {{ props.title }}
      <small class="block text-grey text-xs italic mt-1">
        <slot name="description"></slot>
      </small>
    </div>
    <div class="w-full sm:w-2/3 mb-3 sm:pl-2">
      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    title: {
      type: String,
      required: true
    }
  }
};
</script>
