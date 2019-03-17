<template>
  <div class="uk-grid">
    <div
      class="uk-width-1-1@s"
      :class="{ 'uk-width-5-6@m' : hasToolbar }"
    >
      <h1 class="uk-h1">
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
export default {
  props: {
    title: {
      type: String,
      required: true
    },
    subtitle: {
      type: String,
      required: false
    },
    toolbar: {
      type: Array,
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
