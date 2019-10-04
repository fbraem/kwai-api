<template>
  <div class="flex flex-col">
    <div>
      <h4 class="mb-0">
        {{ story.content.title }}
      </h4>
    </div>
    <div class="flex items-center justify-between">
      <div class="text-meta truncate">
        {{ story.content.summary }}
      </div>
      <div>
        <router-link
          :to="storyLink"
          class="icon-button text-gray-700 hover:bg-gray-300"
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
