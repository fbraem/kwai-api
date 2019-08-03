<template>
  <!-- eslint-disable max-len -->
  <div class="calendar">
    <div
      class="uk-margin-bottom"
      uk-grid
    >
      <div>
        <a
          @click="prevYear"
          class="uk-link-reset uk-icon-button"
        >
          <i class="fas fa-angle-double-left"></i>
        </a>
        <a
          @click="prevMonth"
          class="uk-link-reset uk-icon-button"
        >
          <i class="fas fa-angle-left "></i>
        </a>
      </div>
      <div class="uk-width-expand uk-text-center">
        <span class="uk-h2 uk-text-capitalize">
          {{ monthName }} {{ year }}
        </span>
      </div>
      <div>
        <a
          @click="nextMonth"
          class="uk-link-reset uk-icon-button"
        >
          <i class="fas fa-angle-right"></i>
        </a>
        <a
          @click="nextYear"
          class="uk-link-reset uk-icon-button"
        >
          <i class="fas fa-angle-double-right"></i>
        </a>
      </div>
    </div>
    <ol class="days">
      <li
        v-for="(day, index) in days" :key="index"
        class="day"
        :class="{ 'outside': day.outsideOfCurrentMonth, 'empty': day.events.length === 0 }"
      >
        <div class="date">
          <span class="weekday">
            {{ day.weekday }}
          </span>
          <span class="day">
            {{ day.number }}
          </span>
          <span class="month">
            {{ day.month }}
          </span>
          <span class="year">
            {{ day.year }}
          </span>
        </div>
        <div class="events">
          <div
            v-for="(event, index) in day.events"
            :key="index"
          >
            {{ event.formattedStartTime }}&nbsp;
            <router-link :to="{ name: 'trainings.read', params: { id: event.id }}">
              <del
                v-if="event.event.cancelled"
                :uk-tooltip="'title:' + $t('cancelled')"
              >
                {{ event.content.title }}
              </del>
              <span v-else>
                {{ event.content.title }}
              </span>
            </router-link>
            <i
              v-if="event.event.cancelled"
              :uk-tooltip="'title:' + $t('cancelled')"
              class="fas fa-times"
              style="color: rgb(192,28,24)"
            >
            </i>
            <i
              v-if="event.coaches == null || event.coaches.length == 0"
              :uk-tooltip="'title:' + $t('no_coach')"
              class="fas fa-user-times"
              style="color: rgb(192,28,24)"
            >
            </i>
          </div>
        </div>
      </li>
    </ol>
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

/**
 * Component for showing a calendar with trainings
 */
export default {
  props: {
    /**
     * The year of the calendar
     */
    year: {
      type: Number,
      required: true
    },
    /**
     * The month of the calendar
     */
    month: {
      type: Number,
      required: true
    },
    /**
     * The trainings of this year/month
     */
    trainings: {
      type: Array,
      required: true
    }
  },
  i18n: messages,
  data() {
    return {
      dayNames: moment.weekdays(true),
    };
  },
  computed: {
    currentDate() {
      return moment()
        .year(this.year)
        .month(this.month - 1);
    },
    monthName() {
      return this.currentDate.format('MMMM');
    },
    days() {
      let m = () => {
        return moment()
          .year(this.year)
          .month(this.month - 1)
          .startOf('month');
      };
      let daysInMonth = m().daysInMonth();
      let previousMonthDays = m().date(1).day() - 1;
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
          events: this.trainings.filter((training) => {
            if (training.event) {
              return current.isSame(training.event.start_date, 'day');
            }
            return false;
          })
        });
      }
      return days;
    }
  },
  methods: {
    prevYear() {
      this.$emit('prevYear');
    },
    nextYear() {
      this.$emit('nextYear');
    },
    prevMonth() {
      this.$emit('prevMonth');
    },
    nextMonth() {
      this.$emit('nextMonth');
    }
  }
};
</script>
