<template>
  <!-- eslint-disable max-len -->
  <li class="mb-4">
    <div class="flex flex-no-wrap relative items-center">
      <router-link
        :to="linkToCategory"
        class="cover"
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
      <div style="width: 80%;">
        <h4 class="mb-0">
          {{ category.name }}
        </h4>
        <div class="text-sm">
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
