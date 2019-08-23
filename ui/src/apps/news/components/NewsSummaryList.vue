<template>
  <div style="display:flex; flex-direction:column;">
    <NewsSummaryListItem
      v-for="story in stories"
      :key="story.id"
      :story="story"
    />
  </div>
</template>

<script>
import NewsSummaryListItem from './NewsSummaryListItem';

export default {
  props: {
    stories: {
      type: Array,
      required: true
    }
  },
  components: {
    NewsSummaryListItem
  }
};
</script>
