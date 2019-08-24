<template>
  <div class="kwai-card profile hover">
    <a
      v-if="isLink"
      :href="to"
      class="kwai-card-link">
    </a>
    <router-link
      class="kwai-card-link"
      v-else :to="to"
    >
    </router-link>
    <div class="kwai-card-image">
      <slot name="icon"></slot>
    </div>
    <div class="kwai-card-title">
      <h4 style="margin:0">
        {{ title }}
      </h4>
    </div>
    <div class="kwai-card-text">
      <slot name="description">Please specify a description ...</slot>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    title: {
      type: String,
      required: true
    },
    to: {
      type: [Object, String],
      required: true
    }
  },
  computed: {
    isLink() {
      return typeof this.to === 'string';
    }
  }
};
</script>
