<template>
  <!-- eslint-disable max-len -->
  <div class="flex flex-col shadow-lg">
    <div class="flex flex-col md:flex-row">
      <div class="text-center bg-red-700 text-red-300 p-6 w-full md:w-1/2 self-center">
        <div class="text-4xl leading-none lowercase">
          {{ dayName }}
        </div>
        <div class="text-6xl font-black leading-none">
          {{ day }}
        </div>
        <div class="text-4xl leading-none lowercase">
          {{ month }}
        </div>
      </div>
      <div class="text-center p-6 w-full md:w-1/2 self-center">
        <div class="text-4xl leading-none lowercase">
          {{ training.formattedStartTime }}
        </div>
        <div class="text-4xl leading-none">
          -
        </div>
        <div class="text-4xl leading-none lowercase">
          {{ training.formattedEndTime }}
        </div>
      </div>
    </div>
    <slot></slot>
  </div>
</template>

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
