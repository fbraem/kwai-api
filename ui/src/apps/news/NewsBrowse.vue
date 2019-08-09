<template>
  <div>
    <Page>
      <Spinner v-if="$wait.is('news.browse')" />
      <div
        v-else
        uk-grid
      >
        <div
          v-if="storiesMeta"
          class="uk-width-1-1"
        >
          <Paginator
            :count="storiesMeta.count"
            :limit="storiesMeta.limit"
            :offset="storiesMeta.offset"
            @page="readPage"
          />
        </div>
        <div
          class="uk-child-width-1-1@s uk-child-width-1-2@xl"
          uk-grid
        >
          <NewsCard
            v-for="story in stories"
            :key="story.id"
            :story="story"
            @deleteStory="deleteStory"
          />
        </div>
        <div
          v-if="storiesMeta"
          class="uk-width-1-1"
        >
          <Paginator
            :count="storiesMeta.count"
            :limit="storiesMeta.limit"
            :offset="storiesMeta.offset"
            @page="readPage"
          />
        </div>
      </div>
      <div
        v-if="! $wait.is('news.browse') && newsCount == 0"
        class="uk-width-1-1"
      >
        <div uk-alert>
          {{ $t('no_news') }}
        </div>
      </div>
      <AreYouSure
        id="delete-story"
        :yes="$t('delete')"
        :no="$t('cancel')"
        @sure="doDeleteStory"
      >
        {{ $t('are_you_sure') }}
      </AreYouSure>
    </Page>
  </div>
</template>

<script>
import Page from './Page.vue';
import NewsCard from './components/NewsCard.vue';
import Paginator from '@/components/Paginator.vue';
import AreYouSure from '@/components/AreYouSure.vue';
import Spinner from '@/components/Spinner';

import UIkit from 'uikit';

import messages from './lang';

export default {
  i18n: messages,
  components: {
    Page,
    NewsCard,
    Paginator,
    AreYouSure,
    Spinner
  },
  data() {
    return {
      showAreYouSure: false,
      storyToDelete: null,
      categoryId: null
    };
  },
  computed: {
    stories() {
      return this.$store.state.news.stories;
    },
    storiesMeta() {
      return this.$store.state.news.meta;
    },
    newsCount() {
      if (this.stories) return this.stories.length;
      return -1;
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
    async fetchData(params) {
      if (params.category) {
        this.categoryId = params.category;
        await this.$store.dispatch('category/read', {
          id: params.category
        });
      }
      await this.$store.dispatch('news/browse', {
        year: params.year,
        month: params.month,
        category: params.category,
        featured: params.featured
      });
    },
    deleteStory(story) {
      this.storyToDelete = story;
      var modal = UIkit.modal(document.getElementById('delete-story'));
      modal.show();
    },
    doDeleteStory() {
      this.$store.dispatch('news/delete', {
        story: this.storyToDelete
      });
    },
    async readPage(offset) {
      await this.$store.dispatch('news/browse', {
        offset: offset,
        year: this.year,
        month: this.month,
        category: this.categoryId,
        featured: this.featured
      });
    }
  }
};
</script>
