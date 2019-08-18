<template>
  <!-- eslint-disable max-len -->
  <div style="border: 1px solid #d3d3d3; border-radius: .25rem;">
    <div :style="'background-size: cover; min-height:150px; background-position: center; background-color: #ccc; background-image:url(' + require('@/apps/news/images/exclamation-point-2620923_1920.jpg') + ')'">
    </div>
    <div style="padding:15px">
      <h3>{{ $t('featured_news') }}</h3>
      <NewsSummaryList
        v-if="hasStories"
        :stories="stories"
      />
      <div
        v-else
      >
        <div class="kwai-text-meta">
          {{ $t('no_featured_news') }}
        </div>
        <div class="kwai-text-small">
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
