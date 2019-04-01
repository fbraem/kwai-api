<template>
  <div
    class="uk-grid-small uk-grid-divider"
    uk-grid
  >
    <div
      v-for="story in stories"
      class="uk-width-1-1"
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
