<template>
  <div class="m-2 p-3 relative h-full hover:shadow text-center">
    <a
      v-if="isLink"
      :href="to"
      class="cover"
    >
    </a>
    <router-link v-else
      class="cover"
      :to="to"
    />
    <div>
      <slot name="icon"></slot>
    </div>
    <div>
      <h4 class="mt-2">
        {{ title }}
      </h4>
    </div>
    <div>
      <slot name="text"></slot>
    </div>
  </div>
</template>

<script>
/**
 * Component that show an icon card
 */
export default {
  props: {
    title: {
      type: String,
      required: true
    },
    to: {
      type: [ String, Object ]
    }
  },
  computed: {
    isLink() {
      return typeof this.to === 'string';
    }
  }
};
</script>
