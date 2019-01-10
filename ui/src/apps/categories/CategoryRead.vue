<template>
  <!-- eslint-disable max-len -->
  <div>
    <PageHeader v-if="category" :picture="category.header_picture">
      <div uk-grid>
        <div class="uk-width-expand uk-light">
          <div v-if="category">
            <h1>
              <span v-if="category.icon_picture">
                <img  :src="category.icon_picture" width="40" height="40" uk-svg />&nbsp;
              </span>
              {{ category.name }}</h1>
              <p>
                {{ category.description }}
              </p>
            </div>
          </div>
          <div class="uk-width-1-1 uk-width-1-6@m">
            <div class="uk-flex uk-flex-right">
              <div v-if="$category.isAllowed('create')">
                <router-link  class="uk-icon-button" :to="{ name : 'categories.create' }">
                  <i class="fas fa-plus"></i>
                </router-link>
              </div>
              <div v-if="$category.isAllowed('update', category)">
                <router-link class="uk-icon-button uk-margin-small-left"
                  :to="{ name : 'categories.update', params : { id : category.id } }">
                  <i class="fas fa-edit"></i>
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </PageHeader>
      <section v-if="category" class="uk-section uk-section-default uk-section-small">
        <div class="uk-container uk-container-expand">
          <div uk-grid class="uk-flex uk-margin">
            <div class="uk-width-1-1">
              <h4 class="uk-heading-line"><span>{{ $t('featured_news') }}</span></h4>
              <div v-if="storyCount == 0" class="uk-margin">
                {{ $t('no_featured_news') }}
              </div>
              <div v-else-if="storyCount > 2" uk-slider="velocity: 5; autoplay-interval: 5000;autoplay: true;">
                <div class="uk-position-relative">
                  <div class="uk-slider-container">
                    <ul class="uk-slider-items uk-child-width-1-2@m uk-grid-medium uk-grid" uk-height-match="target: > li > div > .uk-card">
                      <li v-for="story in stories" :key="story.id">
                        <NewsCard :story="story" :showCategory="false"></NewsCard>
                      </li>
                    </ul>
                  </div>
                  <div class="uk-hidden@m uk-light">
                    <a class="uk-position-bottom-left uk-position-small" href="#" uk-slidenav-previous uk-slider-item="previous"></a>
                    <a class="uk-position-bottom-right uk-position-small" href="#" uk-slidenav-next uk-slider-item="next"></a>
                  </div>
                  <div class="uk-visible@m">
                    <a class="uk-position-center-left-out uk-position-small" href="#" uk-slidenav-previous uk-slider-item="previous"></a>
                    <a class="uk-position-center-right-out uk-position-small" href="#" uk-slidenav-next uk-slider-item="next"></a>
                  </div>
                  <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>
                </div>
              </div>
              <div v-else class="uk-child-width-1-1 uk-child-width-1-2@m uk-flex uk-flex-center" uk-grid>
                <NewsCard v-for="story in stories" :story="story" :key="story.id" :showCategory="false"></NewsCard>
              </div>
              <div class="uk-margin">
                <router-link :to="{ name: 'news.browse', params: { category : category.id } }">{{ $t('more_news') }}</router-link>
              </div>
            </div>
            <div v-if="pageCount > 0" class="uk-width-1-1">
              <h4 class="uk-heading-line"><span>Informatie</span></h4>
              <div class="uk-grid-medium uk-child-width-1-2@s uk-child-width-1-3@m uk-child-width-1-4@l uk-grid-match" uk-grid="masonry: true">
                <div v-for="page in pages" :page="page" :key="page.id">
                  <PageSummary :page="page"></PageSummary>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </template>

  <style>
  #icon.svg {
    fill:red;
  }
  </style>

<script>
import messages from './lang';

import categoryStore from '@/stores/categories';
import newsStore from '@/stores/news';
import pageStore from '@/stores/pages';
import registerModule from '@/stores/mixin';

import PageHeader from '@/site/components/PageHeader.vue';
import NewsCard from '@/apps/news/components/NewsCard.vue';
import PageSummary from '@/apps/pages/components/PageSummary.vue';

export default {
  i18n: messages,
  components: {
    NewsCard,
    PageSummary,
    PageHeader
  },
  mixins: [
    registerModule(
      {
        category: categoryStore
      },
      {
        news: newsStore
      },
      {
        pageModule: pageStore
      }
    ),
  ],
  computed: {
    category() {
      return this.$store.getters['category/category'](this.$route.params.id);
    },
    picture() {
      if (this.category && this.category.images) {
        return this.category.images.normal;
      }
      return null;
    },
    stories() {
      return this.$store.getters['news/stories'];
    },
    storyCount() {
      if (this.stories) return this.stories.length;
      return 0;
    },
    pages() {
      return this.$store.getters['pageModule/pages'];
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
      this.$store.dispatch('pageModule/browse', {
        category: params.id
      });
    }
  }
};
</script>
