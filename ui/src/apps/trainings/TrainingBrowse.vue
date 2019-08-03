<template>
  <div>
    <Spinner v-if="$wait.is('training.browse')" />
    <div
      v-else
      uk-grid
    >
      <div class="uk-width-1-1">
        <Calendar
          :year="year"
          :month="month"
          :trainings="trainings"
          @prevMonth="prevMonth"
          @prevYear="prevYear"
          @nextMonth="nextMonth"
          @nextYear="nextYear"
        />
        <div v-if="noData">
          <div class="uk-alert uk-alert-warning">
            {{ $t('training.events.no_data') }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Calendar from './Calendar';
import Spinner from '@/components/Spinner';

import messages from './lang';

export default {
  components: {
    Calendar, Spinner
  },
  props: {
    year: {
      type: Number,
      required: true
    },
    month: {
      type: Number,
      required: true
    }
  },
  i18n: messages,
  computed: {
    trainings() {
      var trainings = this.$store.state.training.trainings;
      return trainings || [];
    },
    noData() {
      return this.trainings.length === 0;
    },
  },
  beforeRouteEnter(to, from, next) {
    next(async(vm) => {
      await vm.fetchData(to.params.year, to.params.month);
      next();
    });
  },
  async beforeRouteUpdate(to, from, next) {
    await this.fetchData(to.params.year, to.params.month);
    next();
  },
  methods: {
    fetchData(year, month) {
      this.$store.dispatch('training/browse', {
        year: year,
        month: month
      });
    },
    prevYear() {
      this.$router.push({
        name: 'trainings.browse',
        params: {
          year: this.year - 1,
          month: this.month
        }
      });
    },
    nextYear() {
      this.$router.push({
        name: 'trainings.browse',
        params: {
          year: this.year + 1,
          month: this.month
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
        name: 'trainings.browse',
        params: {
          year,
          month
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
        name: 'trainings.browse',
        params: {
          year,
          month
        }
      });
    }
  }
};
</script>
