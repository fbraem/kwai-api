<template>
  <!-- eslint-disable max-len -->
  <div>
    <div v-if="$wait.is('events.browse')"
      class="uk-flex-center" uk-grid>
      <div class="uk-text-center">
        <i class="fas fa-spinner fa-2x fa-spin"></i>
      </div>
    </div>
    <div v-else class="uk-child-width-1-1" uk-grid>
      <div class="calendar">
        <div class="title">
          <router-link :to="prevMonth" class="fas fa-caret-left uk-link-reset">
          </router-link>
          <div class="month" style="text-transform:capitalize">
            {{ monthName }}
          </div>
          <div class="year">
            {{ year }}
          </div>
          <router-link :to="nextMonth" class="fas fa-caret-right uk-link-reset">
          </router-link>
        </div>
        <ol class="days">
          <li class="day" v-for="(day, index) in days" :key="index"
            :class="{ 'outside': day.outsideOfCurrentMonth, 'empty': day.events.length === 0 }">
            <div class="date">
              <span class="weekday">{{ day.weekday }}</span>
              <span class="day">{{ day.number }}</span>
              <span class="month">{{ day.month }}</span>
              <span class="year">{{ day.year }}</span>
            </div>
            <div class="events">
              <div v-for="(event, index) in day.events" :key="index">
                {{ event.formattedStartTime }} - {{ event.formattedEndTime }}&nbsp;
                <router-link :to="{ name: 'events.read', params: { id: event.id }}">
                  {{ event.name }}
                </router-link>
              </div>
            </div>
          </li>
        </ol>
      </div>
      <div v-if="noData">
        <div class="uk-alert uk-alert-warning">
          {{ $t('no_data') }}
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.calendar > .title {
  font-size: 2.5rem;
  font-weight: 100;
  margin-bottom: 2rem;
  color: #222;
}
.calendar > .title > * {
  display: inline;
}
.calendar .days {
  list-style: none;
  margin: 0 0 0 0;
  padding: 0;
}
.calendar .days > .day.outside {
  display: none;
}
.calendar .days > .day.empty {
  display: none;
}
.calendar .days .events {
  margin-bottom: 1rem;
}
.calendar .days .events .event {
  box-sizing: border-box;
  line-height: 1;
  /* font-size: .75rem; */
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  background: rgba(182, 43, 16, 0.05);
  color: #B62B10;
  padding: .25rem .5rem;
  margin-bottom: 2px;
  cursor: pointer;
  transition: all .1s ease-in-out;
}
.calendar .days .events .event:hover, .calendar .days .events .event:focus {
  background: rgba(182, 43, 16, 0.1);
}
.calendar .days .events .event:active {
  color: white;
  background: #b62b10;
}
.calendar .days .date {
  position: relative;
  font-size: 1.25rem;
  margin-bottom: 1rem;
  padding-bottom: .5rem;
}
.calendar .days .date:after {
  content: '';
  position: absolute;
  left: 0;
  right: 0;
  bottom: 0;
  height: 1px;
  background: currentColor;
  opacity: .5;
}
.calendar .days .date > * {
  display: inline-block;
}
.calendar .days .date .weekday {
  font-weight: 400;
  color: #B62B10;
  text-transform: uppercase;
}
.calendar .days .date .weekday:after {
  content: ',';
}

@media (min-width: 1024px) {
  .calendar {
    border-bottom: 2px solid #B62B10;
  }
  .calendar .days {
    position: relative;
    display: flex;
    justify-content: flex-start;
    align-items: stretch;
    flex-wrap: wrap;
  }
  .calendar .days > .day {
    position: relative;
    font-size: .75rem;
    margin-bottom: 0;
    padding: 0 0 15% 0;
    width: 14.2857142857%;
    flex-shrink: 0;
  }
  .calendar .days > .day:before {
    content: '';
    position: absolute;
    left: 0;
    right: .5rem;
    top: 0;
    height: 1px;
    background: currentColor;
    opacity: .5;
  }
  .calendar .days > .day .date {
    position: absolute;
    top: 1rem;
    font-size: 1rem;
    line-height: 1rem;
  }
  .calendar .days > .day .date:after {
    display: none;
  }
  .calendar .days > .day .date .weekday,
  .calendar .days > .day .date .month,
  .calendar .days > .day .date .year {
    display: none;
  }
  .calendar .days > .day .date .day:after {
    content: "";
  }
  .calendar .days > .day.outside {
    display: inline-block;
  }
  .calendar .days > .day.outside:before {
    opacity: .125;
  }
  .calendar .days > .day.outside .date .day {
    opacity: 0.5;
  }
  .calendar .days > .day.empty {
    display: inline-block;
  }
  .calendar .days > .day:nth-child(n+1):nth-child(-n+7) {
    margin-top: 2rem;
  }
  .calendar .days > .day:nth-child(n+1):nth-child(-n+7):before {
    opacity: 1;
    height: 2px;
    background: #B62B10;
  }
  .calendar .days > .day:nth-child(n+1):nth-child(-n+7) .date {
    width: 100%;
  }
  .calendar .days > .day:nth-child(n+1):nth-child(-n+7) .date .weekday {
    display: block;
    position: absolute;
    top: -3rem;
    width: 100%;
    overflow: hidden;
    text-transform: uppercase;
    font-weight: 300;
    color: #B62B10;
    text-overflow: ellipsis;
  }
  .calendar .days > .day:nth-child(n+1):nth-child(-n+7) .date .weekday:after {
    content: "";
  }
  .calendar .days > .day .events {
    box-sizing: border-box;
    position: absolute;
    height: 100%;
    width: 100%;
    padding-top: 2.5rem;
    padding-right: .5rem;
    overflow: auto;
  }
  .calendar .days > .day .events .event {
    box-sizing: border-box;
    line-height: 1;
    font-size: .75rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    background: rgba(182, 43, 16, 0.05);
    color: #B62B10;
    padding: .25rem .5rem;
    margin-bottom: 2px;
    cursor: pointer;
    transition: all .1s ease-in-out;
  }
  .calendar .days > .day .events .event:hover,
  .calendar .days > .day .events .event:focus {
    background: rgba(182, 43, 16, 0.1);
  }
  .calendar .days > .day .events .event:active {
    color: white;
    background: #b62b10;
  }
}
</style>

<script>
import moment from 'moment';

import messages from './lang';

import eventStore from '@/stores/events';
import registerModule from '@/stores/mixin';

export default {
  props: {
    year: {
      type: Number,
      default: moment().year()
    },
    month: {
      type: Number,
      default: moment().month() + 1
    }
  },
  i18n: messages,
  mixins: [
    registerModule(
      {
        event: eventStore,
      }
    ),
  ],
  data() {
    return {
      dayNames: moment.weekdays(true),
    };
  },
  computed: {
    currentDate() {
      return moment().year(this.year).month(this.month - 1);
    },
    monthName() {
      return this.currentDate.format('MMMM');
    },
    events() {
      var events = this.$store.state.event.events;
      return events || [];
    },
    days() {
      let m = () => moment().year(this.year).month(this.month - 1).startOf('month')
      let daysInMonth = m().daysInMonth();
      let previousMonthDays = m().date(1).day();
      let offset = 0 - previousMonthDays;
      let nextMonthDays = offset + (6 - m().date(daysInMonth).day());
      let total = daysInMonth + previousMonthDays + nextMonthDays;
      let days = [];

      for (let i = offset; i < total; i++) {
        var current = m().add(i, 'd');
        days.push({
          outsideOfCurrentMonth: (i < 0 || i > daysInMonth - 1),
          date: current,
          weekday: current.format('dddd'),
          day: current.format('Do'),
          number: current.format('D'),
          month: current.format('MMMM'),
          year: current.format('YYYY'),
          events: this.events.filter((event) => {
            return current.isSame(event.start_date, 'day');
          })
        });
      }
      return days;
    },
    noData() {
      return this.events && this.events.length === 0;
    },
    prevMonth() {
      var year = this.year;
      var month = this.month - 1;
      if (month === 0) {
        year = this.year - 1;
        month = 12;
      }
      return {
        to: 'events.browse',
        params: {
          year,
          month
        }
      };
    },
    nextMonth() {
      var year = this.year;
      var month = this.month + 1;
      if (month === 13) {
        year = this.year + 1;
        month = 1;
      }
      return {
        to: 'events.browse',
        params: {
          year,
          month
        }
      };
    }
  },
  beforeRouteEnter(to, from, next) {
    next(async(vm) => {
      await vm.fetchData();
      next();
    });
  },
  watch: {
    '$route'() {
      this.fetchData();
    }
  },
  methods: {
    fetchData() {
      this.$store.dispatch('event/browse', {
        year: this.year,
        month: this.month
      });
    }
  }
};
</script>
