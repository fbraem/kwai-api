<template>
  <div>
    <div style="display: flex;justify-content: center">
      <div
        style="margin:20px;"
        :style="{ 'width': width + '%'}"
        v-for="story in activeStories"
        :key="story.id"
      >
        <NewsCard
          :story="story"
          :showCategory="false"
        />
      </div>
    </div>
    <div style="display: flex; justify-content: space-evenly">
      <div>
        <button
          class="kwai-icon-button"
          @click.prevent.stop="prev"
          :disabled="!hasPrev"
        >
          <i class="fas fa-chevron-circle-left"></i>
        </button>
      </div>
      <div>
        <button
          class="kwai-icon-button"
          @click.prevent.stop="next"
          :disabled="!hasNext"
        >
          <i class="fas fa-chevron-circle-right"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import VueScreenSize from 'vue-screen-size';

import NewsCard from './NewsCard';

export default {
  props: {
    stories: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      start: 1
    };
  },
  computed: {
    count() {
      return this.stories.length;
    },
    columns() {
      if (this.$vssWidth >= 1300) return 3;
      if (this.$vssWidth >= 980) return 2;
      return 1;
    },
    width() {
      if (this.$vssWidth >= 1300) return 100 / 3;
      if (this.$vssWidth >= 980) return 50;
      return 100;
    },
    activeStories() {
      let actives = [];
      let end = this.start + this.columns - 1;
      if (end >= this.count) {
        end = this.count;
      }
      for (let s = this.start - 1; s < end; s++) {
        actives.push(this.stories[s]);
      }
      return actives;
    },
    hasNext() {
      return this.start < this.stories.length;
    },
    hasPrev() {
      return this.start > 1;
    }
  },
  mixins: [
    VueScreenSize.VueScreenSizeMixin,
  ],
  watch: {
    'columns'(nv, ov) {
      if (this.start + this.columns > this.count) {
        this.start = this.count - this.columns + 1;
      }
    }
  },
  components: {
    NewsCard
  },
  methods: {
    next() {
      if (this.start <= this.count - this.columns) this.start += 1;
    },
    prev() {
      if (this.start > 1) this.start -= 1;
    }
  }
};
</script>
