<template>
  <div class="uk-grid-small uk-flex uk-flex-middle" uk-grid>
    <div class="uk-width-expand">
      <router-link
        :to="storyLink"
        class="uk-link-reset"
      >
        <h6 class="uk-h6 uk-margin-small-bottom">
          {{ story.content.title }}
        </h6>
        <div class="uk-text-meta uk-text-truncate">
          {{ story.content.summary }}
        </div>
      </router-link>
    </div>
    <div class="uk-width-auto">
      <router-link
        :to="storyLink"
        class="uk-icon-button uk-link-reset"
      >
        <i class="fas fa-ellipsis-h"></i>
      </router-link>
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
