<template>
  <!-- eslint-disable max-len -->
  <li>
    <div style="display: flex;">
      <div v-if="category.icon_picture">
        <inline-svg
          :src="category.icon_picture"
          width="40"
          height="40"
          fill="#c61c18"
        />
      </div>
      <div style="position:relative">
        <router-link
          :to="linkToCategory"
          class="kwai-cover-link"
        />
        <h4>
          {{ category.name }}
        </h4>
        <div class="kwai-text-meta">
          {{ category.short_description }}
        </div>
      </div>
    </div>
  </li>
</template>

<script>
import InlineSvg from 'vue-inline-svg';

import Category from '@/models/Category';

/**
 * Component for showing a category in the list of categories
 */
export default {
  components: {
    InlineSvg
  },
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
