<template>
  <div class="border rounded border-solid border-gray-400">
    <div
      class="bg-center bg-gray-400 bg-cover"
      :style="backgroundStyle">
    </div>
    <div class="p-2">
      <h3>{{ $t('featured_news') }}</h3>
      <NewsSummaryList
        v-if="hasStories"
        :stories="stories"
      />
      <div
        v-else
      >
        <div class="text-sm text-gray-600">
          {{ $t('no_featured_news') }}
        </div>
        <div class="text-sm text-gray-600">
          <router-link :to="oldNewsLink">
            {{ $t('see_old_news') }}
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Category from '@/models/Category';
import messages from '../lang';
import NewsSummaryList from './NewsSummaryList';

export default {
  props: {
    stories: {
      type: Array,
      required: true
    },
    category: {
      type: Category,
      required: false
    }
  },
  computed: {
    backgroundStyle() {
      return {
        'min-height': '150px',
        'background-image':
          'url('
            + require('@/apps/news/images/exclamation-point-2620923_1920.jpg')
            + ')'
      };
    },
    hasStories() {
      return this.stories.length > 0;
    },
    oldNewsLink() {
      if (this.category) {
        return {
          name: 'news.category',
          params: {
            category: this.category.id
          }
        };
      }
      return {
        name: 'news.browse'
      };
    }
  },
  components: {
    NewsSummaryList
  },
  i18n: messages
};
</script>
