<template>
  <div class="flex flex-col">
    <div>
      <h4 class="header-line">
        {{ $t('overview') }}
      </h4>
    </div>
    <div>
      <ul class="pl-0">
        <CategoryListItem
          v-for="(category, index) in categories"
          :category="category"
          :key="index"
        />
      </ul>
    </div>
  </div>
</template>

<script>
import messages from '../lang';

import CategoryListItem from './CategoryListItem.vue';

/**
 * Component for showing a list of categories
 */
export default {
  /**
   * Categories to show in the list
   */
  props: {
    categories: {
      type: Array,
      required: true
    }
  },
  components: {
    CategoryListItem
  },
  i18n: messages
};
</script>
