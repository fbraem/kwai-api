<template>
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
        {{ training.formattedStartTime}}
      </div>
      <div style="font-size:4em; line-height:1em; text-transform:lowercase;">
        -
      </div>
      <div style="font-size:4em; text-transform:lowercase;">
        {{ training.formattedEndTime}}
      </div>
    </div>
  </div>
</template>

<style lang="scss">
@import '@/site/scss/_mq.scss';

.training-container {
  display: flex;
  flex-direction: row;
  @include mq($until: tablet) {
    flex-direction: column;
  }
}

.training-day {
  text-align: center;
  background-color: var(--kwai-color-secondary);
  color: var(--kwai-color-secondary-text);
  padding: 40px;
}

.training-hour {
  text-align: center;
  padding: 40px;
}
</style>

<script>
import Training from '@/models/trainings/Training';

export default {
  props: {
    training: {
      type: Training,
      required: true,
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
    }
  }
};
</script>
