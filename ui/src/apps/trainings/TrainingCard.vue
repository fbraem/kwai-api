<template>
  <div class="training-card">
    <div class="training-container">
      <div class="training-day">
        <div style="font-size:2em; line-height:1em; text-transform:lowercase;">
          {{ dayName }}
        </div>
        <div style="font-size:8em; font-weight:900; line-height:1em;">
          {{ day }}
        </div>
        <div style="font-size:2em; line-height:1em; text-transform:lowercase;">
          {{ month }}
        </div>
      </div>
      <div class="training-hour">
        <div style="font-size:4em; line-height:1em; text-transform:lowercase;">
          {{ training.formattedStartTime }}
        </div>
        <div style="font-size:4em; line-height:1em; text-transform:lowercase;">
          -
        </div>
        <div style="font-size:4em; text-transform:lowercase;">
          {{ training.formattedEndTime }}
        </div>
        <br />
      </div>
    </div>
    <slot></slot>
  </div>
</template>

<style lang="scss">
@import '@/site/scss/_mq.scss';

.training-card {
  display: flex;
  box-shadow: 0 5px 15px rgba(0,0,0,.08);
  flex-direction: column;
}

.training-container {
  display: flex;
  flex-direction: row;
  @include mq($until: tablet) {
    flex-direction: column;
  }
}

.training-day {
  text-align: center;
  background-color: var(--kwai-color-primary-bg);
  color: var(--kwai-color-primary-fg);
  padding: 40px;
  flex-grow: 1;
  flex-basis: 0;
}

.training-hour {
  text-align: center;
  padding: 40px;
  flex-grow: 1;
  flex-basis: 0;
}
</style>

<script>
import messages from './lang';

import Training from '@/models/trainings/Training';

export default {
  i18n: messages,
  props: {
    training: {
      type: Training,
      required: true
    }
  },
  computed: {
    day() {
      return this.training.event.start_date.date();
    },
    dayName() {
      return this.training.event.start_date.format('dddd');
    },
    month() {
      return this.training.event.start_date.format('MMMM');
    },
    error() {
      return this.$store.state.training.error;
    },
    notAllowed() {
      return this.error && this.error.response.status === 401;
    },
    notFound() {
      return this.error && this.error.response.status === 404;
    },
  }
};
</script>
