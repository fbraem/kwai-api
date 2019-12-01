<!-- A row with Icon Butons -->
<template>
  <div class="flex flex-row">
    <template v-for="(button, index) in toolbar">
      <template v-if="button.route">
        <router-link
          class="mr-1 last:mr-0 icon-button"
          :class="buttonClass"
          :to="button.route"
          :key="index"
        >
          <i :class="button.icon"></i>
        </router-link>
      </template>
      <template v-if="button.method">
        <a
          :key="index"
          class="mr-1 last:mr-0 icon-button"
          :class="buttonClass"
          @click.prevent.stop="button.method"
        >
          <i :class="button.icon"></i>
        </a>
      </template>
    </template>
  </div>
</template>

<script>
export default {
  props: {
    toolbar: {
      type: Array,
      required: true
    },
    normalClass: {
      type: String,
      required: true
    },
    hoverClass: {
      type: String,
      required: true
    },
  },
  computed: {
    buttonClass() {
      return {
        [this.normalClass]: true,
        [this.hoverClass]: true
      };
    }
  }
};
</script>
