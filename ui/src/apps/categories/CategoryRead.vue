<template>
  <!-- eslint-disable max-len -->
    <div uk-grid class="uk-flex uk-margin">
      <div class="uk-width-1-1">
        <h4 class="uk-heading-line">
          <span>{{ $t('featured_news') }}</span>
        </h4>
        <div
          v-if="$wait.is('news.browse')"
          class="uk-flex-center"
          uk-grid
        >
          <div class="uk-text-center">
            <i class="fas fa-spinner fa-2x fa-spin"></i>
          </div>
        </div>
        <div
          v-if="storyCount == 0"
          class="uk-margin"
        >
          {{ $t('no_featured_news') }}
        </div>
        <div v-if="stories">
          <NewsSlider :stories="stories" />
        </div>
        <div
          v-if="category"
          class="uk-margin"
        >
          <router-link
            :to="moreNewsLink"
            class="uk-button uk-button-default"
          >
            {{ $t('more_news') }}
          </router-link>
        </div>
      </div>
      <div
        v-if="$wait.is('pages.browse')"
        class="uk-flex-center"
        uk-grid
      >
        <div class="uk-text-center">
          <i class="fas fa-spinner fa-2x fa-spin"></i>
        </div>
      </div>
      <div
        v-if="pageCount > 0"
        class="uk-width-1-1"
      >
        <h4 class="uk-heading-line">
          <span>Informatie</span>
        </h4>
        <div
          class="uk-grid-medium uk-child-width-1-2@s uk-child-width-1-3@m uk-child-width-1-4@l uk-grid-match"
          uk-grid="masonry: true"
        >
          <div
            v-for="page in pages"
            :page="page"
            :key="page.id"
          >
            <PageSummary :page="page" />
          </div>
        </div>
      </div>
    </div>
  </template>

  <style>
  #icon.svg {
    fill:red;
  }
  </style>

<script>
import messages from './lang';

import NewsCard from '@/apps/news/components/NewsCard.vue';
import NewsSlider from '@/apps/news/components/NewsSlider.vue';
import PageSummary from '@/apps/pages/components/PageSummary.vue';

/**
 * Page for showing category news and information
 */
export default {
  i18n: messages,
  components: {
    NewsCard,
    NewsSlider,
    PageSummary
  },
  computed: {
    category() {
      return this.$store.getters['category/category'](this.$route.params.id);
    },
    moreNewsLink() {
      return {
        name: 'news.category',
        params: {
          category: this.category.id
        }
      };
    },
    stories() {
      return this.$store.state.news.stories;
    },
    storyCount() {
      if (this.stories) return this.stories.length;
      return 0;
    },
    pages() {
      return this.$store.state.page.pages;
    },
    pageCount() {
      if (this.pages) return this.pages.length;
      return 0;
    },
    pageLink() {
      return {
        name: 'pages.read',
        params: {
          id: this.page.id
        }
      };
    }
  },
  beforeRouteEnter(to, from, next) {
    next(async(vm) => {
      await vm.fetchData(to.params);
      next();
    });
  },
  async beforeRouteUpdate(to, from, next) {
    await this.fetchData(to.params);
    next();
  },
  methods: {
    fetchData(params) {
      this.$store.dispatch('category/read', {
        id: params.id
      });
      this.$store.dispatch('news/browse', {
        category: params.id,
        featured: true
      });
      this.$store.dispatch('page/browse', {
        category: params.id
      });
    }
  }
};
</script>
