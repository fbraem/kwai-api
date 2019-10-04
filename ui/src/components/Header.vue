<template>
  <div class="hero-container mx-auto text-gray-500">
    <div
      v-if="picture"
      class="self-center"
      style="grid-area: hero-image"
    >
      <img :src="picture" class="m-auto" />
    </div>
    <div style="grid-area: hero-text;">
      <div v-if="badge">
        <router-link
          :to="badge.route"
          class="badge red-badge mb-4 no-underline hover:no-underline"
        >
          {{ badge.name }}
        </router-link>
      </div>
      <router-link
        v-if="route && title"
        :to="route"
        class="hover:no-underline"
      >
        <h1 class="flex items-center mb-2">
          <inline-svg
            v-if="logo"
            :src="logo"
            width="42"
            height="42"
            fill="white"
            class="mr-3"
          />
          {{ title }}
        </h1>
      </router-link>
      <h1
        v-else-if="title"
        class="flex items-center mb-2"
      >
        <inline-svg
          v-if="logo"
          :src="logo"
          width="42"
          height="42"
          fill="white"
          class="mr-3"
        />
        {{ title }}
      </h1>
      <h2 v-if="subtitle">
        {{ subtitle }}
      </h2>
      <slot></slot>
    </div>
    <div
      v-if="hasToolbar"
      class="flex flex-row"
      style="grid-area: hero-toolbar;"
    >
      <template v-for="(button, index) in toolbar">
        <template v-if="button.route">
          <router-link
            class="mr-1 last:mr-0 icon-button header-icon-button"
            :to="button.route"
            :key="index"
          >
            <i :class="button.icon"></i>
          </router-link>
        </template>
        <template v-if="button.method">
          <a
            :key="index"
            class="mr-1 last:mr-0 icon-button header-icon-button"
            @click.prevent.stop="button.method"
          >
            <i :class="button.icon"></i>
          </a>
        </template>
      </template>
    </div>
  </div>
</template>

<style scoded>
.hero-container {
    display: grid;
    @apply p-4;
    grid-gap: 10px;
    grid-template-columns: auto;
    grid-template-rows: auto 1fr auto;
    grid-template-areas:
        "hero-image"
        "hero-text"
        "hero-toolbar"
    ;
    @apply bg-gray-800;
}

@screen md {
  .hero-container {
      grid-template-columns: fit-content(800px) 1fr auto;
      grid-template-rows: auto auto;
      grid-template-areas:
          "hero-image hero-text hero-toolbar"
      ;
  }
}

.hero-container h1, h2 {
  @apply text-white;
}

.red-badge {
  @apply bg-red-700 text-red-300;
}

.header-icon-button {
  @apply text-gray-300;
}

.header-icon-button:hover {
  @apply bg-gray-900;
}

</style>

<script>
import InlineSvg from 'vue-inline-svg';

/**
 * Component for a header of a page
 */
export default {
  props: {
    /**
     * Title of the header
     */
    title: {
      type: String
    },
    /**
     * A badge to show
     */
    badge: {
      type: Object
    },
    /**
     * Picture to show in the header
     */
    picture: {
      type: String
    },
    /**
     * Sub title
     */
    subtitle: {
      type: String,
      required: false
    },
    /**
     * toolbar
     */
    toolbar: {
      type: Array,
      required: false
    },
    /**
     * Logo
     */
    logo: {
      type: String,
      required: false
    },
    /**
     * Link
     */
    route: {
      type: Object,
      required: false
    }
  },
  components: {
    InlineSvg
  },
  computed: {
    hasToolbar() {
      return this.toolbar && this.toolbar.length > 0;
    }
  }
};
</script>
