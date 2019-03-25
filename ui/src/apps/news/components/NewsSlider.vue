<template>
  <div>
    <div
      v-if="count > 2"
      uk-slider="velocity: 5; autoplay-interval: 5000;autoplay: true;"
    >
      <div class="uk-position-relative">
        <div class="uk-slider-container">
          <ul
            class="uk-slider-items uk-child-width-1-2@m uk-grid-medium uk-grid"
            uk-height-match="target: > li > div > .uk-card"
          >
            <li
              v-for="story in stories"
              :key="story.id"
            >
              <NewsCard
                :story="story"
                :showCategory="false"
              />
            </li>
          </ul>
        </div>
        <div class="uk-hidden@m uk-light">
          <a
            class="uk-position-bottom-left uk-position-small"
            href="#"
            uk-slidenav-previous
            uk-slider-item="previous"
          >
          </a>
          <a
            class="uk-position-bottom-right uk-position-small"
            href="#"
            uk-slidenav-next
            uk-slider-item="next"
          >
          </a>
        </div>
        <div class="uk-visible@m">
          <a
            class="uk-position-center-left-out uk-position-small"
            href="#"
            uk-slidenav-previous
            uk-slider-item="previous"
          >
          </a>
          <a
            class="uk-position-center-right-out uk-position-small"
            href="#"
            uk-slidenav-next
            uk-slider-item="next"
          >
          </a>
        </div>
        <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin">
        </ul>
      </div>
    </div>
    <div
      v-else
      class="uk-child-width-1-1@s uk-child-width-1-2@m uk-flex uk-flex-center"
      uk-grid
    >
      <NewsCard
        v-for="story in stories"
        :story="story"
        :key="story.id"
        :showCategory="false"
      />
    </div>
  </div>
</template>

<script>
import NewsCard from './NewsCard';

export default {
  props: {
    stories: {
      type: Array,
      required: true
    }
  },
  computed: {
    count() {
      return this.stories.length;
    }
  },
  components: {
    NewsCard
  }
};
</script>
