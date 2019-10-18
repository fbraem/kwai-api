<template>
  <!-- eslint-disable max-len -->
  <div class="news-card">
    <div style="grid-area: news-card-title">
      <h3 class="mb-0">
        <router-link
          v-if="story.content"
          :to="contentLink"
          class="no-underline hover:no-underline"
          style="color: inherit;"
        >
          {{ story.content.title }}
        </router-link>
        <span v-else>
          {{ story.content.title }}
        </span>
      </h3>
      <div
        v-if="story.publish_date"
        class="text-xs text-gray-600"
      >
        {{ $t('published', {
          publishDate: story.localPublishDate,
          publishDateFromNow: story.publishDateFromNow
          })
        }}
      </div>
    </div>
    <div
      v-if="story.overview_picture"
      style="grid-area: news-card-image;"
    >
      <img
        :src="story.overview_picture"
        alt=""
        class="rounded w-32 h-32"
      />
    </div>
    <div style="grid-area: news-card-content">
      <router-link
        v-if="showCategory"
        class="badge red-badge no-underline hover:no-underline mb-1"
        :to="categoryLink"
        >
        {{ story.category.name }}
      </router-link>
      <div
       v-html="story.content.html_summary"
       >
      </div>
    </div>
    <div style="grid-area: news-card-tools;justify-self:right">
      <router-link
        v-if="story.content"
        class="icon-button text-gray-700 hover:bg-gray-300"
        :to="contentLink"
      >
        <i class="fas fa-ellipsis-h"></i>
      </router-link>
      <router-link
        v-if="$can('update', story)"
        :to="{ name : 'news.update', params : { id : story.id }}"
        class="icon-button text-gray-700 hover:bg-gray-300"
      >
        <i class="fas fa-edit"></i>
      </router-link>
      <a
        v-if="$can('delete', story)"
        @click="deleteStory"
        class="icon-button text-gray-700 hover:bg-gray-300"
      >
        <i class="fas fa-trash"></i>
      </a>
    </div>
  </div>
</template>

<style scoped>
.news-card {
  @apply h-full shadow p-3;
  display: grid;
  grid-gap: 10px;

  grid-template-columns: 1fr;
  grid-template-rows: auto auto 1fr auto;
  grid-template-areas:
      "news-card-title"
      "news-card-image"
      "news-card-content"
      "news-card-tools"
  ;
}
@screen sm {
  .news-card {
    grid-template-columns: auto 1fr;
    grid-template-rows: auto 1fr auto;
    grid-template-areas:
        "news-card-title news-card-title"
        "news-card-image news-card-content"
        "news-card-image news-card-tools"
    ;
  }
}
.red-badge {
  @apply bg-red-700 text-red-300;
}
</style>

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
