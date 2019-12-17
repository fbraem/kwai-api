<template>
  <!-- eslint-disable max-len -->
  <div class="flex flex-wrap items-stretch w-full mb-4 relative">
    <div
      v-if="left"
      class="flex -mr-px"
    >
      <span class="flex items-center leading-normal bg-grey-lighter rounded rounded-l-none border border-r-0 border-grey-light px-3 whitespace-no-wrap text-grey-dark text-sm">
        <i :class="left"></i>
      </span>
    </div>
    <slot></slot>
    <div
      v-if="right"
      class="flex -mr-px">
      <span class="flex items-center leading-normal bg-grey-lighter rounded rounded-r-none border border-l-0 border-grey-light px-3 whitespace-no-wrap text-grey-dark text-sm">
        <i :class="right"></i>
      </span>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    left: {
      type: String
    },
    right: {
      type: String
    }
  }
};
</script>
