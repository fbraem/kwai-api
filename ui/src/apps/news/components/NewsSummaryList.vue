<template>
  <div class="flex flex-col">
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
