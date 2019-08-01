<template>
  <div class="uk-margin-left">
    <div
      class="uk-card uk-card-hover uk-card-body"
      uk-scrollspy="cls:uk-animation-slide-top-small"
    >
      <a
        v-if="isLink"
        :href="to"
        class="uk-link-reset uk-position-cover uk-position-z-index uk-margin-remove-adjacent">
      </a>
      <router-link
        v-else :to="to"
        class="uk-link-reset uk-position-cover uk-position-z-index uk-margin-remove-adjacent">
      </router-link>
      <slot name="icon"></slot>
      <h4 class="uk-margin">
        {{ title }}
      </h4>
      <div class="uk-margin">
          <slot name="description">Please specify a description ...</slot>
      </div>
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
