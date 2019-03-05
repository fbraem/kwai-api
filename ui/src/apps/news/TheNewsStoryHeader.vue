<template>
  <!-- eslint-disable max-len -->
  <div uk-grid>
    <div
      v-if="picture"
      class="uk-width-1-1 uk-width-1-2@m uk-width-2-3@l uk-width-3-5@xl uk-flex uk-flex-middle"
    >
      <div>
        <img :src="picture" />
      </div>
    </div>
    <div
      class="uk-width-1-1"
      :class="contentClass"
    >
      <div
        v-if="story"
        uk-grid
      >
        <div class="uk-width-expand">
          <div class="uk-card uk-card-body">
            <div
              class="uk-card-badge uk-label"
              style="font-size: 0.75rem;background-color:#c61c18;color:white"
            >
              <router-link
                :to="categoryNewsLink"
                class="uk-link-reset"
              >
                {{ story.category.name }}
              </router-link>
            </div>
            <div class="uk-light">
              <h1 class="uk-margin-remove">
                {{ $t('news')}}
              </h1>
              <h2 class="uk-margin-remove">
                {{ story.content.title }}
              </h2>
              <div
                v-if="story.publish_date"
                class="uk-article-meta"
              >
                {{ $t('published', { publishDate : story.localPublishDate, publishDateFromNow : story.publishDateFromNow }) }}
              </div>
            </div>
          </div>
        </div>
        <div class="uk-width-1-1 uk-width-1-6@m">
          <div class="uk-flex uk-flex-right">
            <div v-if="$can('update', story)" class="uk-margin-small-left">
              <router-link
                :to="storyLink"
                class="uk-icon-button uk-link-reset"
              >
                <i class="fas fa-edit"></i>
              </router-link>
            </div>
            <div
              v-if="$can('delete', story)"
              class="uk-margin-small-left"
            >
              <a
                uk-toggle="target: #delete-story"
                class="uk-icon-button uk-link-reset"
              >
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

export default {
  i18n: messages,
  computed: {
    story() {
      return this.$store.getters['news/story'](this.$route.params.id);
    },
    picture() {
      if (this.story) {
        return this.story.detail_picture;
      }
      return null;
    },
    contentClass() {
      return {
        'uk-width-1-2@m': this.picture != null,
        'uk-width-1-3@l': this.picture != null,
        'uk-width-2-5@xl': this.picture != null
      };
    },
    storyLink() {
      return {
        name: 'news.update',
        params: {
          id: this.story.id
        }
      };
    },
    categoryNewsLink() {
      return {
        name: 'news.category',
        params: {
          category: this.story.category.id
        }
      };
    }
  }
};
</script>
