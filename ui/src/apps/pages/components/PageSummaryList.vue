<template>
  <div
    class="uk-grid-small uk-grid-divider"
    uk-grid
  >
    <div
      v-for="page in pages"
      class="uk-width-1-1"
      :key="page.id"
    >
      <PageSummaryListItem :page="page" />
    </div>
  </div>
</template>

<script>
import PageSummaryListItem from './PageSummaryListItem';

export default {
  props: {
    pages: {
      type: Array,
      required: true
    }
  },
  components: {
    PageSummaryListItem
  }
};
</script>
