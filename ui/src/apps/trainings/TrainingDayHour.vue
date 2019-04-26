<template>
  <div
    class="uk-grid-collapse"
    uk-grid
  >
    <div
      class="uk-width-1-2@m uk-light uk-text-center uk-padding"
      style="background-color:rgb(198, 28, 24)"
    >
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
    <div class="uk-width-1-2@m uk-text-center uk-padding">
      <div style="font-size:4em; line-height:1em; text-transform:lowercase;">
        {{ training.formattedStartTime}}
      </div>
      <div style="font-size:4em; line-height:1em; text-transform:lowercase;">
        -
      </div>
      <div style="font-size:4em; text-transform:lowercase;">
        {{ training.formattedEndTime}}
      </div>
      <br />
    </div>
  </div>
</template>

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
