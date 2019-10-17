<template>
  <!-- eslint-disable max-len -->
  <div
    class="h-full flex flex-col border border-gray-500 shadow-lg overflow-hidden rounded"
    :class="color"
  >
    <div
      class="px-6 py-4"
      :class="textColor"
    >
      <slot name="header">
        <h4>{{ title }}</h4>
        <p
          v-if="note"
          class="text-xs truncate m-0 p-0"
        >
          {{ note }}
        </p>
      </slot>
    </div>
    <div class="border-1 bg-white flex-grow">
      <slot></slot>
    </div>
    <div
      class="flex flex-wrap justify-between px-3 py-2"
      :class="textColor"
    >
      <div class="text-xs flex flex-wrap items-center">
        <slot name="footer"></slot>
      </div>
      <div
        v-if="hasToolbar"
        class="ml-auto"
      >
        <template v-for="(button, index) in toolbar">
          <template v-if="button.route">
            <router-link
              class="icon-button text-gray-700 hover:bg-gray-300"
              :to="button.route"
              :key="index"
            >
              <i :class="button.icon"></i>
            </router-link>
          </template>
          <template v-if="button.method">
            <a
              :key="index"
              class="icon-button text-gray-700 hover:bg-gray-300"
              @click.prevent.stop="button.method"
            >
              <i :class="button.icon"></i>
            </a>
          </template>
        </template>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    toolbar: {
      type: Array,
      required: true
    },
    color: {
      type: String,
      default: 'bg-tatami'
    },
    'text-color': {
      type: String,
      default: 'text-white'
    },
    title: {
      type: String,
      required: false
    },
    note: {
      type: String,
      required: false
    }
  },
  computed: {
    hasToolbar() {
      return this.toolbar && this.toolbar.length > 0;
    }
  }
};
</script>
