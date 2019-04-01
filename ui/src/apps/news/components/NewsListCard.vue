<template>
  <div class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header">
      <h3 class="uk-card-title">{{ $t('featured_news') }}</h3>
    </div>
    <div class="uk-card-body">
      <NewsSummaryList
        v-if="hasStories"
        :stories="stories"
      />
      <div
        v-else
        class="uk-grid-small"
        uk-grid
      >
        <div class="uk-width-1-1 uk-text-meta">
          {{ $t('no_featured_news') }}
        </div>
        <div class="uk-width-1-1 uk-text-small">
          <router-link :to="oldNewsLink">
            {{ $t('see_old_news') }}
          </router-link>
        </div>
      </div>
    </div>
    <div class="uk-card-footer">
      <router-link :to="{ name: 'news.browse' }">
        {{ $t('more_news')}}
      </router-link>
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
