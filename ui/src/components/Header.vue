<template>
  <div class="bg-gray-800 text-gray-500">
    <div class="container mx-auto">
      <div class="hero-container">
        <div
          v-if="picture"
          class="self-center"
          style="grid-area: hero-image;"
        >
          <img :src="picture" class="m-auto sm:m-0" />
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
            class="text-gray-500 hover:no-underline"
          >
            <h1 class="flex items-center mb-2 text-white">
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
            class="flex items-center mb-2 text-white"
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
          class="flex flex-row justify-end mr-2"
          style="grid-area: hero-toolbar"
        >
          <IconButtons
            :toolbar="toolbar"
            normalClass="text-gray-300"
            hoverClass="hover:bg-gray-900"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
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
}

@screen xl {
  .hero-container {
      grid-template-columns: fit-content(800px) 1fr auto;
      grid-template-rows: auto auto;
      grid-template-areas:
          "hero-image hero-text hero-toolbar"
      ;
  }
}

.hero-container > h1,
.hero-container > h2 {
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
import IconButtons from './IconButtons';

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
    InlineSvg, IconButtons
  },
  computed: {
    hasToolbar() {
      return this.toolbar && this.toolbar.length > 0;
    }
  }
};
</script>
