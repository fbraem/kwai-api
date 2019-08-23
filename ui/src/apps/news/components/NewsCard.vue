<template>
  <!-- eslint-disable max-len -->
  <div class="kwai-news-card">
    <h3 style="grid-area: news-card-title; margin-bottom:0px">
      <router-link
        v-if="story.content"
        class="kwai-link-reset"
        :to="contentLink"
      >
        {{ story.content.title }}
      </router-link>
      <span v-else>
        {{ story.content.title }}
      </span>
    </h3>
    <div
      v-if="story.overview_picture"
      style="grid-area: news-card-image;justify-self:center;"
    >
      <img
        :src="story.overview_picture"
        alt=""
        style="height:150px;width:150px;"
      />
    </div>
    <div style="grid-area: news-card-content">
      <div v-if="showCategory"
        class="kwai-badge kwai-theme-secondary"
      >
        <router-link
          :to="categoryLink"
          class="kwai-link-reset">
          {{ story.category.name }}
        </router-link>
      </div>
      <div
       v-html="story.content.html_summary"
       >
      </div>
    </div>
    <div style="grid-area: news-card-tools;justify-self:right">
      <router-link
        v-if="story.content"
        class="kwai-icon-button"
        :to="contentLink"
      >
        <i class="fas fa-ellipsis-h"></i>
      </router-link>
      <router-link
        v-if="$can('update', story)"
        :to="{ name : 'news.update', params : { id : story.id }}"
        class="kwai-icon-button"
      >
        <i class="fas fa-edit"></i>
      </router-link>
      <a
        v-if="$can('delete', story)"
        @click="deleteStory"
        class="kwai-icon-button"
      >
        <i class="fas fa-trash"></i>
      </a>
    </div>
  </div>
</template>

<script>
import Story from '@/models/Story';
import messages from '../lang';

/**
 * Component for a news story card
 */
export default {
  i18n: messages,
  props: {
    /**
     * The story
     */
    story: {
      type: Story,
      required: true
    },
    /**
     * Show the category label?
     */
    showCategory: {
      type: Boolean,
      default: true
    }
  },
  computed: {
    contentLink() {
      return {
        name: 'news.story',
        params: {
          id: this.story.id
        }
      };
    },
    category() {
      /* eslint-disable max-len */
      return this.$store.getters['categoryModule/category'](this.story.category.id);
    },
    categoryLink() {
      return {
        name: 'news.category',
        params: {
          category: this.story.category.id
        }
      };
    },
  },
  methods: {
    deleteStory() {
      this.$emit('deleteStory', this.story);
    }
  }
};
</script>
