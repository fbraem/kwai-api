<template>
  <div class="kwai-card profile hover">
    <router-link
      class="kwai-card-link"
      :to="linkToCategory"
    >
    </router-link>
    <div v-if="category.icon_picture" class="kwai-card-image">
      <img
        :src="category.icon_picture"
        style="color:#c61c18;"
        width="48"
        height="48"
        uk-svg
      />
    </div>
    <div class="kwai-card-title">
      <h4 style="margin:0">
        {{ category.name }}
      </h4>
    </div>
    <div class="kwai-card-text">
      {{ category.short_description }}
    </div>
  </div>
  <!-- eslint-disable max-len -->
  <!--
  <div class="uk-margin-left uk-margin-right">
    <div
      class="uk-card uk-card-hover uk-card-body"
      uk-scrollspy="cls:uk-animation-slide-top-small"
    >
      <router-link
        :to="linkToCategory"
        class="uk-link-reset uk-position-cover uk-position-z-index uk-margin-remove-adjacent"
      />
      <img
        v-if="category.icon_picture"
        :src="category.icon_picture"
        style="color:#c61c18;"
        width="48"
        height="48"
        uk-svg
      />
      <h4 class="uk-margin">
        {{ category.name }}
      </h4>
      <div class="uk-margin">
        {{ category.short_description }}
      </div>
    </div>
  </div>
  -->
</template>

<script>
/**
 * Component that show a category Card
 */
export default {
  props: {
    /**
     * The category to show in the card.
     */
    category: {
      type: Object,
      required: true
    }
  },
  computed: {
    linkToCategory() {
      return {
        name: 'categories.read',
        params: {
          id: this.category.id
        }
      };
    }
  }
};
</script>
