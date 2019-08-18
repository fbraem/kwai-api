<template>
  <!-- eslint-disable max-len -->
  <div class="hero-container">
    <div v-if="picture">
      <img :src="picture" />
    </div>
    <div v-if="story">
      <div class="kwai-badge kwai-theme-secondary">
        <router-link
          class="kwai-link-reset"
          :to="categoryNewsLink"
        >
          {{ story.category.name }}
        </router-link>
      </div>
      <div>
        <h1>{{ $t('news')}}</h1>
        <h2>{{ story.content.title }}</h2>
        <div
          v-if="story.publish_date"
          class="kwai-article-meta"
        >
          {{ $t('published', { publishDate : story.localPublishDate, publishDateFromNow : story.publishDateFromNow }) }}
        </div>
      </div>
      <div style="display:flex; justify-content:flex-end;flex-flow:row;padding:5px">
        <router-link
          v-if="$can('update', story)"
          :to="storyLink"
          class="kwai-icon-button kwai-theme-muted"
        >
          <i class="fas fa-edit"></i>
        </router-link>
        <a
          v-if="$can('delete', story)"
          class="kwai-icon-button kwai-theme-muted"
          style="margin-left: 5px;"
        >
          <i class="fas fa-trash"></i>
        </a>
      </div>
    </div>
  </div>
</template>

<script>
import messages from './lang';

export default {
  i18n: messages,
  computed: {
    story() {
      return this.$store.getters['news/story'](this.$route.params.id);
    },
    picture() {
      if (this.story) {
        return this.story.detail_picture;
      }
      return null;
    },
    storyLink() {
      return {
        name: 'news.update',
        params: {
          id: this.story.id
        }
      };
    },
    categoryNewsLink() {
      return {
        name: 'news.category',
        params: {
          category: this.story.category.id
        }
      };
    }
  }
};
</script>
