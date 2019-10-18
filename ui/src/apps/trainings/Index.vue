<template>
  <!-- eslint-disable max-len -->
  <div class="container mx-auto py-3 px-3 sm:px-0">
    <div
      class="training-grid-container"
      style="justify-items: center;"
    >
      <div
        class="max-w-full md:max-w-xl p-4"
        style="grid-area: news-area;"
      >
        <NewsListCard
          :stories="stories"
          :category="category"
          class="h-full"
        />
      </div>
      <div
        class="max-w-full md:max-w-xl p-4"
        style="grid-area: info-area;"
      >
        <PageListCard
          :pages="pages"
          class="h-full"
        />
      </div>
      <div
        class="w-full mb-4"
        style="grid-area: trainers-area;"
      >
        <CoachListCard
          :coaches="coaches"
        />
      </div>
    </div>
    <div style="grid-area: calendar-area">
      <div class="flex flex-row">
        <div class="flex-grow">
          <h3>Kalender</h3>
        </div>
        <div>
          <router-link
            class="icon-button text-gray-700 hover:bg-gray-300"
            :to="calendarLink"
          >
            <i class="fas fa-angle-up"></i>
          </router-link>
        </div>
      </div>
      <Calendar
        :year="year"
        :month="month"
        :trainings="trainings"
        @prevMonth="prevMonth"
        @prevYear="prevYear"
        @nextMonth="nextMonth"
        @nextYear="nextYear"
      />
    </div>
  </div>
</template>

<style scoped>

.training-grid-container {
  display: grid;
  grid-gap: 20px;
  grid-template-columns: minmax(0, 1fr);
  grid-template-rows: 1fr;
  grid-template-areas:
    "news-area"
    "info-area"
    "trainers-area"
    "calendar-area"
  ;
}

@screen md {
  .training-grid-container {
    grid-template-columns: 1fr 1fr;
    grid-template-rows: 1fr auto auto;
    grid-template-areas:
      "news-area info-area"
      "trainers-area trainers-area"
      "calendar-area calendar-area"
    ;
  }
}
</style>

<script>

import moment from 'moment';

import NewsListCard from '@/apps/news/components/NewsListCard';
import PageListCard from '@/apps/pages/components/PageListCard';
import Calendar from '@/apps/trainings/Calendar';
import CoachListCard from './components/CoachListCard';

export default {
  components: {
    NewsListCard,
    PageListCard,
    Calendar,
    CoachListCard
  },
  data() {
    return {
      year: moment().year(),
      month: moment().month() + 1,
    };
  },
  computed: {
    category() {
      return this.$store.getters['category/categoryApp'](this.$route.meta.app);
    },
    stories() {
      return this.$store.state.news.stories || [];
    },
    hasStories() {
      return this.stories.length > 0;
    },
    pages() {
      return this.$store.state.page.pages || [];
    },
    coaches() {
      return this.$store.state.training.coach.coaches || [];
    },
    trainings() {
      return this.$store.state.training.trainings || [];
    },
    calendarLink() {
      return {
        name: 'trainings.browse',
        params: {
          year: this.year,
          month: this.month
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
    async fetchData(params) {
      this.$store.dispatch('news/browse', {
        category: this.category.id,
        featured: true
      });
      this.$store.dispatch('page/browse', {
        category: this.category.id
      });
      this.$store.dispatch('training/browse', {
        year: this.year,
        month: this.month
      });
      this.$store.dispatch('training/coach/browse');
    },
    prevYear() {
      this.year -= 1;
      this.$store.dispatch('training/browse', {
        year: this.year,
        month: this.month
      });
    },
    nextYear() {
      this.year += 1;
      this.$store.dispatch('training/browse', {
        year: this.year,
        month: this.month
      });
    },
    prevMonth() {
      this.month = this.month - 1;
      if (this.month === 0) {
        this.year = this.year - 1;
        this.month = 12;
      }
      this.$store.dispatch('training/browse', {
        year: this.year,
        month: this.month
      });
    },
    nextMonth() {
      this.month = this.month + 1;
      if (this.month === 13) {
        this.year = this.year + 1;
        this.month = 1;
      }
      this.$store.dispatch('training/browse', {
        year: this.year,
        month: this.month
      });
    }
  }
};
</script>
