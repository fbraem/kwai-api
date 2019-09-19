<template>
  <!-- eslint-disable max-len -->
    <div class="page-container">
      <div style="grid-column: 1 / 3;">
        <h4 class="kwai-header-line">
          {{ $t('featured_news') }}
        </h4>
        <Spinner v-if="$wait.is('news.browse')" />
        <div v-if="storyCount == 0">
          {{ $t('no_featured_news') }}
        </div>
        <div v-if="stories">
          <NewsSlider :stories="stories" />
        </div>
      </div>
      <div style="grid-column: 1 / 3;justify-self:center;">
        <router-link
          :to="moreNewsLink"
          class="kwai-button"
        >
          {{ $t('more_news') }}
        </router-link>
      </div>
      <Spinner v-if="$wait.is('pages.browse')" />
      <div
        style="grid-column: 1 / 3;"
        v-if="pageCount > 0"
      >
        <h4 class="kwai-header-line">
          Informatie
        </h4>
        <div>
          <div
            v-for="page in pages"
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

import NewsSlider from '@/apps/news/components/NewsSlider';
import PageSummary from '@/apps/pages/components/PageSummary';
import Spinner from '@/components/Spinner';

/**
 * Page for showing category news and information
 */
export default {
  i18n: messages,
  components: {
    NewsSlider,
    PageSummary,
    Spinner
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
