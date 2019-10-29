<template>
  <Page>
    <Spinner v-if="$wait.is('news.browse')" />
    <div
      v-else
    >
      <div
        v-if="storiesMeta"
        class="flex justify-center mb-4"
      >
        <Paginator
          :count="storiesMeta.count"
          :limit="storiesMeta.limit"
          :offset="storiesMeta.offset"
          @page="readPage"
        />
      </div>
      <div class="flex flex-wrap justify-center mb-4">
         <div
            v-for="story in stories"
            :key="story.id"
            class="p-2 w-full xl:w-1/2"
          >
            <NewsCard
              :story="story"
              @deleteStory="deleteStory"
            />
          </div>
      </div>
      <div
        v-if="storiesMeta"
        class="flex justify-center"
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
    >
      <Alert type="danger">
        {{ $t('no_news') }}
      </Alert>
    </div>
    <AreYouSure
      :show="showAreYouSure"
      @close="showAreYouSure = false;"
      :yes="$t('delete')"
      :no="$t('cancel')"
      @sure="doDeleteStory"
    >
      {{ $t('are_you_sure') }}
    </AreYouSure>
    <template slot="sidebar">
      <Sidebar />
    </template>
  </Page>
</template>

<script>
import Page from '@/components/Page';
import Sidebar from './Sidebar';
import NewsCard from './components/NewsCard.vue';
import Paginator from '@/components/Paginator.vue';
import AreYouSure from '@/components/AreYouSure.vue';
import Spinner from '@/components/Spinner';
import Alert from '@/components/Alert';

import messages from './lang';

export default {
  i18n: messages,
  components: {
    Page,
    NewsCard,
    Paginator,
    AreYouSure,
    Spinner,
    Alert,
    Sidebar
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
      this.showAreYouSure = true;
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
