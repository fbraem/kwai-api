<template>
  <!-- eslint-disable max-len -->
  <div class="page-container">
    <div style="grid-column: span 2; display: flex; flex-direction: row; flex-wrap: wrap; justify-content: space-around;">
      <NewsListCard
        :stories="stories"
        :category="category"
        class="training-card"
      />
      <PageListCard
        :pages="pages"
        class="training-card"
      />
      <CoachListCard
        :coaches="coaches"
        class="training-card"
      />
    </div>
    <div style="grid-column: span 2; display: flex; flex-direction: row;">
      <div style="flex-grow: 1;">
        <h3>Kalender</h3>
      </div>
      <div>
        <router-link
          class="kwai-icon-button"
          :to="calendarLink"
        >
          <i class="fas fa-angle-up"></i>
        </router-link>
      </div>
    </div>
    <div style="grid-column: span 2;">
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

<style lang="scss" scoped>
@import '@/site/scss/_mq.scss';

.training-card {
  margin: 20px;

  @include mq($from: wide) {
    width: 30%;
    max-width: 450px;
  }
  @include mq($from: desktop, $until: wide) {
    width: 300px;
  }
  @include mq($until: desktop) {
    width: 100%;
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
