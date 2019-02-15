<template>
  <div uk-grid>
    <div v-if="picture" class="uk-width-1-1 uk-width-1-2@m uk-width-2-3@l uk-width-3-5@xl uk-flex uk-flex-middle">
      <div>
        <img :src="picture" />
      </div>
    </div>
    <div class="uk-width-1-1" :class="{ 'uk-width-1-2@m' : picture != null, 'uk-width-1-3@l' : picture != null, 'uk-width-2-5@xl' : picture != null }">
      <div v-if="story" uk-grid>
        <div class="uk-width-expand">
          <div class="uk-card uk-card-body">
            <div class="uk-card-badge uk-label" style="font-size: 0.75rem;background-color:#c61c18;color:white">
              <router-link :to="{ name : 'news.category', params : { category : story.category.id }}" class="uk-link-reset">
                {{ story.category.name }}
              </router-link>
            </div>
            <div class="uk-light">
              <h1 class="uk-margin-remove">{{ $t('news')}}</h1>
              <h2 class="uk-margin-remove">{{ story.title }}</h2>
              <div class="uk-article-meta" v-if="story.publish_date">{{ $t('published', { publishDate : story.localPublishDate, publishDateFromNow : story.publishDateFromNow }) }}</div>
            </div>
          </div>
        </div>
        <div class="uk-width-1-1 uk-width-1-6@m">
          <div class="uk-flex uk-flex-right">
            <div v-if="$story.isAllowed('update', story)" class="uk-margin-small-left">
              <router-link :to="{ name : 'news.update', params : { id : story.id }}" class="uk-icon-button uk-link-reset">
                <i class="fas fa-edit"></i>
              </router-link>
            </div>
            <div v-if="$story.isAllowed('remove', story)" class="uk-margin-small-left">
              <a uk-toggle="target: #delete-story" class="uk-icon-button uk-link-reset">
                <i class="fas fa-trash"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import messages from './lang';

import newsStore from '@/stores/news';
import registerModule from '@/stores/mixin';

export default {
  i18n: messages,
  mixins: [
    registerModule(
      {
        news: newsStore
      }
    ),
  ],
  computed: {
    story() {
      return this.$store.getters['news/story'](this.$route.params.id);
    },
    picture() {
      console.log(this.story);
      if (this.story) {
        return this.story.detail_picture;
      }
      return null;
    },
  }
};
</script>
