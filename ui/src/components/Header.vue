<template>
  <div class="hero-container">
    <div :style="gridStyle">
      <router-link
        v-if="route"
        :to="route"
        class="kwai-link-reset"
      >
        <h1>
          <inline-svg
            v-if="logo"
            :src="logo"
            width="42"
            height="42"
            fill="white"
          />
          {{ title }}
        </h1>
      </router-link>
      <h1 v-else>
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
    </div>
    <div
      v-if="hasToolbar"
      class="kwai-buttons"
      style="display: flex; justify-content: flex-end; flex-flow: row;"
    >
      <template v-for="(button, index) in toolbar">
        <template v-if="button.route">
          <router-link
            class="kwai-icon-button kwai-theme-muted"
            :to="button.route"
            :key="index"
          >
            <i :class="button.icon"></i>
          </router-link>
        </template>
        <template v-if="button.method">
          <a
            :key="index"
            class="kwai-icon-button kwai-theme-muted"
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
    },
    gridStyle() {
      if (this.hasToolbar) {
        return {
          'grid-column': '1'
        };
      }
      return {
        'grid-column': 'span 2'
      };
    }
  }
};
</script>
