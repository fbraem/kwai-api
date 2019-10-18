<template>
  <Page>
    <Spinner v-if="$wait.is('news.read')" />
    <article
      v-if="story"
      class="container mx-auto"
    >
      <h1>{{ story.content.title }}</h1>
      <blockquote>
        <div v-html="story.content.html_summary"></div>
      </blockquote>
      <div class="news-content" v-html="story.content.html_content">
      </div>
      <vue-goodshare-facebook class="no-underline hover:no-underline"
        :title_social="$t('share')"
        :page_url="facebookUrl"
        has_icon />
    </article>
    <template slot="sidebar">
      <Sidebar />
    </template>
  </Page>
</template>

<style>
blockquote {
  @apply bg-gray-200 border-l-8 border-solid border-gray-600 ml-2 mb-4 p-2;
}

.news-content ul {
    list-style-position: inside;
    margin-bottom: 20px;
}

.news-content blockquote {
  @apply bg-gray-200 border-l-8 border-solid border-gray-600 ml-2 mb-4 p-2;
  quotes: "\201C""\201D""\2018""\2019";
}

.news-content .gallery {
    background: #eee;
    column-count: 1;
    column-gap: 1em;
    padding-left: 1em;
    padding-top: 1em;
    padding-right: 1em;
}

@screen lg {
  .news-content .gallery {
    column-count: 2;
  }
}
@screen xl {
  .news-content .gallery {
      column-count: 4;
  }
}

.news-content .gallery .item {
    background: white;
    display: inline-block;
    margin: 0 0 1em;
    width: 100%;
    padding: 1em;
}
</style>

<script>
import messages from './lang';

import VueGoodshareFacebook from 'vue-goodshare/src/providers/Facebook.vue';

import Page from '@/components/Page';
import Sidebar from './Sidebar';
import Spinner from '@/components/Spinner';

export default {
  components: {
    Page,
    Sidebar,
    VueGoodshareFacebook,
    Spinner
  },
  i18n: messages,
  computed: {
    story() {
      return this.$store.getters['news/story'](this.$route.params.id);
    },
    facebookUrl() {
      // TODO: remove the host
      return 'https://www.judokwaikemzeke.be/facebook/news/' + this.story.id;
    },
    error() {
      return this.$store.state.news.error;
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
      try {
        this.$store.dispatch('news/read', {
          id: params.id
        });
      } catch (error) {
        console.log(error);
      }
    }
  }
};
</script>
