<template>
  <!-- eslint-disable max-len -->
  <div class="w-full rounded-lg shadow-lg flex flex-col">
    <div class="flex flex-wrap">
      <div class="flex-shrink-0 mx-auto sm:ml-8">
        <img
          class="h-32 w-32 md:h-48 md:w-48 rounded-full -mt-8 border-8 border-white"
          :src="image"
        >
      </div>
      <div class="flex-shrink flex-grow p-3">
        <slot name="header"></slot>
      </div>
    </div>
    <div class="ml-8 mt-3">
      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    image: {
      type: String,
      required: true
    }
  }
};
</script>
