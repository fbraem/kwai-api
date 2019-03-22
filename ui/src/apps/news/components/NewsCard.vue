<template>
  <!-- eslint-disable max-len -->
  <div>
    <div
      class="uk-width-1-1 uk-card uk-card-body uk-card-default uk-card-small uk-border-rounded"
      style="box-shadow:none;border:  1px solid rgba(0,0,0,0.075);"
    >
      <div
        v-if="showCategory"
        class="uk-card-badge uk-label"
        style="font-size: 0.75rem;background-color:#c61c18;"
      >
        <router-link
          :to="categoryLink"
          class="uk-link-reset">
          {{ story.category.name }}
        </router-link>
      </div>
      <div
        :class="{ 'uk-margin-medium-top' : showCategory }"
        uk-grid
      >
        <div>
          <div
            class="uk-grid uk-grid-medium uk-flex uk-flex-middle"
            uk-grid
          >
            <div
              v-if="story.overview_picture"
              class="uk-width-1-3@s uk-width-2-5@m uk-width-4-6@l uk-height-1-1"
            >
              <img
                :src="story.overview_picture"
                alt=""
              />
            </div>
            <div :class="widthClass">
              <h3 class="uk-card-title uk-margin-small-top uk-margin-remove-bottom uk-text-break">
                <router-link
                  v-if="story.content"
                  class="uk-link-reset"
                  :to="contentLink"
                >
                  {{ story.content.title }}
                </router-link>
                <span v-else>
                  {{ story.content.title }}
                </span>
              </h3>
              <span
                v-if="story.publish_date"
                class="uk-article-meta"
              >
                {{ $t('published', {
                  publishDate: story.localPublishDate,
                  publishDateFromNow: story.publishDateFromNow
                  })
                }}
              </span>
              <div
               v-html="story.content.html_summary"
               class="uk-margin-small"
               >
              </div>
            </div>
          </div>
        </div>
        <div class="uk-width-1-1 uk-margin-remove-top">
          <div class="uk-grid-small" uk-grid>
            <div class="uk-width-expand"></div>
            <div class="uk-width-auto">
              <div>
                <router-link
                  v-if="story.content"
                  class="uk-icon-button uk-link-reset"
                  :to="contentLink"
                >
                  <i class="fas fa-ellipsis-h"></i>
                </router-link>
                <router-link
                  v-if="$can('update', story)"
                  :to="{ name : 'news.update', params : { id : story.id }}"
                  class="uk-icon-button uk-link-reset"
                >
                  <i class="fas fa-edit"></i>
                </router-link>
                <a
                  v-if="$can('delete', story)"
                  @click="deleteStory"
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
  </div>
</template>

<script>
import Story from '@/models/Story';
import messages from '../lang';

/**
 * Component for a news story card
 */
export default {
  i18n: messages,
  props: {
    /**
     * The story
     */
    story: {
      type: Story,
      required: true
    },
    /**
     * Show the category label?
     */
    showCategory: {
      type: Boolean,
      default: true
    }
  },
  computed: {
    contentLink() {
      return {
        name: 'news.story',
        params: {
          id: this.story.id
        }
      };
    },
    category() {
      /* eslint-disable max-len */
      return this.$store.getters['categoryModule/category'](this.story.category.id);
    },
    categoryLink() {
      return {
        name: 'news.category',
        params: {
          category: this.story.category.id
        }
      };
    },
    widthClass() {
      if (this.story.overview_picture) {
        return {
          'uk-width-2-3@s': true,
          'uk-width-3-5@m': true,
          'uk-width-4-6@l': true
        };
      }
      return {
        'uk-width-1-1': true
      };
    }
  },
  methods: {
    deleteStory() {
      this.$emit('deleteStory', this.story);
    }
  }
};
</script>
