<template>
  <div class="uk-grid-divider" uk-grid>
    <div class="uk-width-1-1 uk-width-1-3@s">
      <NewsListCard
        :stories="stories"
        :category="category"
      />
    </div>
    <div class="uk-width-1-1 uk-width-1-3@s">
      <PageListCard :pages="pages" />
    </div>
    <div class="uk-width-1-1 uk-width-1-3@s">
      <CoachListCard :coaches="coaches" />
    </div>
    <div class="uk-width-1-1">
      <div class="uk-margin" uk-grid>
        <div class="uk-width-expand">
          <h3>Kalender</h3>
        </div>
        <div class="uk-width-auto">
          <router-link
            class="uk-icon-button uk-link-reset"
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
        @firstMonth="firstMonth"
        @nextMonth="nextMonth"
        @lastMonth="lastMonth"
      />
    </div>
  </div>
</template>

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
      month: moment().month() + 1
    };
  },
  computed: {
    category() {
      return this.$store.getters['category/category']('2');
    },
    stories() {
      return this.$store.state.news.stories || [];
    },
    hasStories() {
      return this.stories.length > 0;
    },
    pages() {
      return this.$store.state.page.pages;
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
    fetchData(params) {
      this.$store.dispatch('category/read', {
        id: '2'
      });
      this.$store.dispatch('news/browse', {
        category: '2',
        featured: true
      });
      this.$store.dispatch('page/browse', {
        category: '2'
      });
      this.$store.dispatch('training/browse', {
        year: this.year,
        month: this.month
      });
      this.$store.dispatch('training/coach/browse');
    },
    firstMonth() {
      this.$store.dispatch('training/browse', {
        year: this.year,
        month: 1
      });
    },
    lastMonth() {
      this.$store.dispatch('training/browse', {
        year: this.year,
        month: 12
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
