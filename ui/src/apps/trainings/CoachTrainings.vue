<template>
  <div>
    <h2>{{ $t('training.coaches.trainings') }}</h2>
    <Calendar :year="year" :month="month" :trainings="trainings"
      @prevMonth="prevMonth" @firstMonth="firstMonth"
      @nextMonth="nextMonth" @lastMonth="lastMonth" />
  </div>
</template>

<script>
import Calendar from './Calendar.vue';

import messages from './lang';

export default {
  components: {
    Calendar
  },
  props: {
    year: {
      type: Number
    },
    month: {
      type: Number
    }
  },
  i18n: messages,
  computed: {
    trainings() {
      return this.$store.state.training.trainings || [];
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
    fetchData({ id, year, month }) {
      this.$store.dispatch('training/browse', {
        year,
        month,
        coach: id
      });
    },
    firstMonth() {
      this.$router.push({
        name: 'trainings.coaches.trainings',
        params: {
          year: this.year,
          month: 1,
          id: this.$route.params.id
        }
      });
    },
    lastMonth() {
      this.$router.push({
        name: 'trainings.coaches.trainings',
        params: {
          year: this.year,
          month: 12,
          id: this.$route.params.id
        }
      });
    },
    prevMonth() {
      var year = this.year;
      var month = this.month - 1;
      if (month === 0) {
        year = this.year - 1;
        month = 12;
      }
      this.$router.push({
        name: 'trainings.coaches.trainings',
        params: {
          year,
          month,
          id: this.$route.params.id
        }
      });
    },
    nextMonth() {
      var year = this.year;
      var month = this.month + 1;
      if (month === 13) {
        year = this.year + 1;
        month = 1;
      }
      this.$router.push({
        name: 'trainings.coaches.trainings',
        params: {
          year,
          month,
          id: this.$route.params.id
        }
      });
    }
  }
};
</script>
