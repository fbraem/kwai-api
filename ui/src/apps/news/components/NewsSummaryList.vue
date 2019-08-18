<template>
  <div style="display:flex; flex-direction:column;">
    <div
      v-for="story in stories"
      :key="story.id"
    >
      <NewsSummaryListItem :story="story" />
    </div>
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
