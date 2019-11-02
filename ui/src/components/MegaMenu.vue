<template>
  <div class="container mx-auto w-full flex flex-wrap justify-between mx-2">
    <div class="w-full text-white mb-8">
      <h1
        v-if="title"
        class="font-bold text-2xl"
      >
        {{ title }}
      </h1>
      <slot name="subtitle">
        <p v-if="subtitle">
          {{ subtitle }}
        </p>
      </slot>
    </div>
    <slot></slot>
  </div>
</template>

<script>
export default {
  props: {
    title: {
      type: String
    },
    subtitle: {
      type: String
    }
  }
};
</script>
