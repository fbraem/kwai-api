<template>
  <!-- eslint-disable max-len -->
  <div style="display: flex; flex-direction:column;">
    <div>
      <h4 style="margin-bottom: 0px;">
        {{ story.content.title }}
      </h4>
    </div>
    <div style="display: flex; align-items: center; justify-content: space-between;">
      <div class="kwai-text-meta kwai-text-truncate">
        {{ story.content.summary }}
      </div>
      <div>
        <router-link
          :to="storyLink"
          class="kwai-icon-button"
        >
          <i class="fas fa-ellipsis-h"></i>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import Story from '@/models/Story';

export default {
  props: {
    story: {
      type: Story,
      required: true
    }
  },
  computed: {
    storyLink() {
      return {
        name: 'news.story',
        params: {
          id: this.story.id
        }
      };
    }
  }
};
</script>
