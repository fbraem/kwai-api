<template>
  <div style="display:flex; flex-direction:column;">
    <PageSummaryListItem
      v-for="page in pages"
      :key="page.id"
      :page="page"
    />
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
