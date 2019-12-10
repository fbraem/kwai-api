<template>
  <!-- eslint-disable max-len -->
  <div class="container mx-auto flex flex-col">
    <div class="flex flex-wrap">
      <div class="w-full md:w-1/2 p-4">
        <NewsListCard
          :stories="stories"
          :category="category"
          class="h-full"
        />
      </div>
      <div class="w-full md:w-1/2 p-4">
        <PageListCard
          :pages="pages"
          class="h-full"
        />
      </div>
    </div>
    <div class="w-full mb-4 p-3">
      <CoachListCard
        :coaches="coaches"
      />
    </div>
    <div class="w-full p-3">
      <div class="flex flex-row">
        <div class="flex-grow">
          <h3 class="header-line mb-4">
            Kalender
          </h3>
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

<script>
import moment from 'moment';

import newsStore from '@/apps/news/store';
import pageStore from '@/apps/pages/store';

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
      return this.$store.state.training.news.all || [];
    },
    hasStories() {
      return this.stories.length > 0;
    },
    pages() {
      return this.$store.state.training.page.all || [];
    },
    coaches() {
      return this.$store.state.training.coach.all || [];
    },
    trainings() {
      return this.$store.state.training.all || [];
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
  beforeCreate() {
    this.$store.registerModule(['training', 'news'], newsStore);
    this.$store.registerModule(['training', 'page'], pageStore);
  },
  destroyed() {
    this.$store.unregisterModule(['training', 'news']);
    this.$store.unregisterModule(['training', 'page']);
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
      this.$store.dispatch('training/news/browse', {
        category: this.category.id,
        featured: true
      });
      this.$store.dispatch('training/page/browse', {
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
