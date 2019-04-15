<template>
  <div uk-grid>
    <div
      class="uk-width-1-1@s"
      :class="{ 'uk-width-5-6@m' : hasToolbar }"
    >
      <router-link
        v-if="route"
        :to="route"
        class="uk-link-reset"
      >
        <h1 class="uk-h1">
          <img
            v-if="logo"
            :src="logo"
            width="42"
            height="42"
            uk-svg
            style="margin-top:-10px"
          />
          {{ title }}
        </h1>
      </router-link>
      <h1 v-else class="uk-h1">
        <img
          v-if="logo"
          :src="logo"
          width="42"
          height="42"
          uk-svg
          style="margin-top:-10px"
        />
        {{ title }}
      </h1>
      <h3
        v-if="subtitle"
        class="uk-h3 uk-margin-remove"
      >
        {{ subtitle }}
      </h3>
    </div>
    <div
      v-if="hasToolbar"
      class="uk-width-1-1@s uk-width-1-6@m"
    >
      <div class="uk-flex uk-flex-right">
        <template v-for="(button, index) in toolbar">
          <div
            :class="{ 'uk-margin-small-left': index > 0 }"
            :key="index"
          >
            <router-link
              class="uk-icon-button uk-link-reset"
              :to="button.route"
            >
              <i :class="button.icon"></i>
            </router-link>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script>
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
  computed: {
    hasToolbar() {
      return this.toolbar && this.toolbar.length > 0;
    }
  }
};
</script>
