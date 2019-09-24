<template>
  <!-- eslint-disable max-len -->
  <!-- TODO: move style to scss file -->
  <div
    style="display: grid; grid-template-rows: 48px 1fr 1fr; padding: 10px; position: relative; height: 100%;"
    class="kwai-hover-border"
  >
    <a
      v-if="isLink"
      :href="to"
      class="kwai-cover-link"
    >
    </a>
    <router-link v-else
      class="kwai-cover-link"
      :to="to"
    />
    <div style="grid-row: 1; text-align: center">
      <slot name="icon"></slot>
    </div>
    <div style="grid-row: 2; text-align:center">
      <h4 style="margin-top:20px">
        {{ title }}
      </h4>
    </div>
    <div style="grid-row: 3; text-align:center">
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
