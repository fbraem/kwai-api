<template>
  <!-- eslint-disable max-len -->
  <div class="w-full relative flex flex-col md:flex-row border rounded">
    <div
      v-if="story.overview_picture"
      class="self-center md:self-start py-2 md:py-0 flex-none"
    >
      <router-link
        :to="contentLink"
      >
        <img
          :src="story.overview_picture"
          alt=""
          class="rounded w-64 h-64 md:w-48 md:h-48"
        />
      </router-link>
    </div>
    <div class="w-full p-2 md:px-4 flex flex-col md:h-48 bg-gray-100 relative">
      <span
        v-if="story.isNew"
        class="ml-2 mt-2 mr-4 mb-1 badge right-0 top-0 absolute red-badge"
      >
        Nieuw
      </span>
      <h2
        class="font-bold text-lg font-bold mb-0"
        :class="{ 'w-3/4': story.isNew, 'sm:w-4/5': story.isNew }"
      >
        <router-link
          :to="contentLink"
          class="text-gray-900"
        >
          {{ story.content.title }}
        </router-link>
      </h2>
      <p class="text-xs text-gray-600">
        <span v-if="showCategory">
          Gepubliceerd in
          <router-link
            :to="categoryLink"
            class="font-bold"
          >
            {{ story.category.name }}
          </router-link>
          &nbsp;|&nbsp;
        </span>
        {{
          $t('published', {
            publishDate: story.localPublishDate,
            publishDateFromNow: story.publishDateFromNow
          })
        }}
      </p>
      <div
        class="text-gray-700 text-sm"
        v-html="story.content.html_summary"
      >
      </div>
      <div class="absolute right-0 bottom-0 mb-2 mr-4">
        <router-link
          :to="contentLink"
          class="text-gray-700"
        >
          <i class="fas fa-ellipsis-h"></i>
        </router-link>
      </div>
    </div>
  </div>
</template>

<style scoped>
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
