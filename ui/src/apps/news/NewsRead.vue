<template>
  <!-- eslint-disable max-len -->
  <div>
    <Page>
      <Spinner v-if="$wait.is('news.read')" />
      <article v-if="story">
        <blockquote>
          <div v-html="story.content.html_summary"></div>
        </blockquote>
        <div class="news-content" v-html="story.content.html_content">
        </div>
        <AreYouSure id="delete-story" :yes="$t('delete')" :no="$t('cancel')" @sure="deleteStory">
        {{ $t('are_you_sure') }}
        </AreYouSure>
        <vue-goodshare-facebook style="text-decoration:none"
          :title_social="$t('share')"
          :page_url="facebookUrl"
          has_icon />
      </article>
    </Page>
  </div>
</template>

<style>
    .news-content ul {
        list-style-position: inside;
        margin-bottom: 20px;
    }

    blockquote {
      background: #f9f9f9;
      border-left: 10px solid #ccc;
      margin: 1.5em 10px;
      padding: 0.5em 10px;
      quotes: "\201C""\201D""\2018""\2019";
    }
    .gallery {
        background: #eee;
        column-count: 4;
        column-gap: 1em;
        padding-left: 1em;
        padding-top: 1em;
        padding-right: 1em;
    }
    .gallery .item {
        background: white;
        display: inline-block;
        margin: 0 0 1em;
        width: 100%;
        padding: 1em;
    }
    @media (max-width: 1200px) {
      .gallery {
      column-count: 4;
      }
    }
    @media (max-width: 1000px) {
      .gallery {
          column-count: 3;
      }
    }
    @media (max-width: 800px) {
      .gallery {
          column-count: 2;
      }
    }
    @media (max-width: 400px) {
      .gallery {
          column-count: 1;
      }
    }
</style>

<script>
import messages from './lang';

import VueGoodshareFacebook from 'vue-goodshare/src/providers/Facebook.vue';

import Page from './Page.vue';
import AreYouSure from '@/components/AreYouSure.vue';
import Spinner from '@/components/Spinner';

export default {
  components: {
    Page,
    AreYouSure,
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
    },
    deleteStory() {
      this.$store.dispatch('news/delete', {
        story: this.story
      }).then(() => {
        this.$router.push({ name: 'news.browse' });
      });
    }
  }
};
</script>
