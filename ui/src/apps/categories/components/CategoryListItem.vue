<template>
  <!-- eslint-disable max-len -->
  <li>
    <div style="display: flex; position: relative; flex-wrap: no-wrap; align-items: center;">
      <router-link
        :to="linkToCategory"
        class="kwai-cover-link"
      />
      <div
        v-if="category.icon_picture"
        style="width: 20%;"
      >
        <inline-svg
          :src="category.icon_picture"
          width="40"
          height="40"
          fill="#c61c18"
        />
      </div>
      <div style="margin-left: 20px; width: 80%;">
        <h4 style="margin-bottom: 10px;">
          {{ category.name }}
        </h4>
        <div
          style="grid-area: category-description"
          class="kwai-text-meta"
        >
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
