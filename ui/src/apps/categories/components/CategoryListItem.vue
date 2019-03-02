<template>
  <!-- eslint-disable max-len -->
  <li>
    <div class="uk-flex uk-flex-middle uk-inline" uk-grid>
      <div
        v-if="category.icon_picture"
        class="uk-width-auto"
      >
        <img
          :src="category.icon_picture"
          style="color:#c61c18;height:32px;"
          width="40" height="32" uk-svg
        />
      </div>
      <div class="uk-width-expand">
        <router-link
          :to="linkToCategory"
          class="uk-link-reset uk-position-cover uk-position-z-index uk-margin-remove-adjacent"
        />
        <h4 class="uk-h4 uk-margin-remove">
          {{ category.name }}
        </h4>
        <div class="uk-text-meta">
          {{ category.short_description }}
        </div>
      </div>
    </div>
  </li>
</template>

<script>
import Category from '@/models/Category';

/**
 * Component for showing a category in the list of categories
 */
export default {
  props: {
    /**
     * The category
     */
    category: {
      type: Category,
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
