<template>
  <!-- eslint-disable max-len -->
  <div uk-grid class="uk-margin-left uk-grid-small uk-child-width-1-1">
    <div>
      <h4 class="uk-heading-line uk-text-bold">
        <span>
          {{ $t('overview') }}
        </span>
      </h4>
    </div>
    <div>
      <ul class="uk-list uk-list-divider">
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
