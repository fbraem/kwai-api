<template>
  <!-- eslint-disable max-len -->
  <div class="hero-container">
    <div
      v-if="picture"
      style="grid-area: hero-image"
    >
      <img :src="picture" style="margin: auto; "/>
    </div>
    <div style="grid-area: hero-text;">
      <div
        v-if="badge"
        class="primary:kwai-badge"
        style="margin-bottom: 20px;"
      >
        <router-link :to="badge.route">
          {{ badge.title }}
        </router-link>
      </div>
      <router-link
        v-if="route"
        :to="route"
        class="kwai-link-reset"
      >
        <h1 style="display: flex; align-items: center; margin-bottom: 20px;">
          <inline-svg
            v-if="logo"
            :src="logo"
            width="42"
            height="42"
            fill="white"
            style="margin-right: 1rem;"
          />
          {{ title }}
        </h1>
      </router-link>
      <h1
        v-else
        style="display: flex; align-items: center; margin-bottom: 20px;"
      >
        <inline-svg
          v-if="logo"
          :src="logo"
          width="42"
          height="42"
          fill="white"
        />
        {{ title }}
      </h1>
      <h3 v-if="subtitle">
        {{ subtitle }}
      </h3>
      <slot></slot>
    </div>
    <div
      v-if="hasToolbar"
      class="kwai-buttons"
      style="grid-area: hero-toolbar; display: flex; justify-content: flex-end; flex-flow: row;"
    >
      <template v-for="(button, index) in toolbar">
        <template v-if="button.route">
          <router-link
            class="secondary:kwai-icon-button"
            :to="button.route"
            :key="index"
          >
            <i :class="button.icon"></i>
          </router-link>
        </template>
        <template v-if="button.method">
          <a
            :key="index"
            class="secondary:kwai-icon-button"
            @click.prevent.stop="button.method"
          >
            <i :class="button.icon"></i>
          </a>
        </template>
      </template>
    </div>
  </div>
</template>

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
      type: String,
      required: true
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
