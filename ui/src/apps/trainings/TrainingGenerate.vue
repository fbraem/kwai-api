<template>
  <!-- eslint-disable max-len -->
  <div class="mt-6">
    <Alert
      v-if="count === 0"
      type="warning"
    >
      {{ $t('training.events.no_generated_data') }}
    </Alert>
    <div v-else>
      <Alert type="info">
        {{ $t('training.generator.help') }}
      </Alert>
      <div class="mt-6">
        <table class="w-full border-collapse text-left">
          <tr class="bg-gray-500 border-b border-gray-200">
            <th class="py-2 px-3 font-bold uppercase text-sm text-white">
              <input
                type="checkbox"
                v-model="selectAll"
              />
            </th>
            <th class="py-2 px-3 font-bold uppercase text-sm text-white">
              {{ $t('training.events.day') }}
            </th>
            <th class="py-2 px-3 font-bold uppercase text-sm text-white">
              {{ $t('training.events.date') }}
            </th>
            <th class="py-2 px-3 font-bold uppercase text-sm text-white">
              {{ $t('training.events.time') }}
            </th>
            <th class="py-2 px-3 font-bold uppercase text-sm text-white">
              {{ $t('training.events.coaches') }}
            </th>
          </tr>
          <tr
            v-for="(training, index) in trainings"
            :key="index"
            class="odd:bg-gray-200 border-b border-gray-400"
          >
            <td class="py-2 px-3 text-gray-700">
              <input
                type="checkbox"
                v-model="selectedTrainings"
                :value="index"
              />
            </td>
            <td class="py-2 px-3 text-gray-700">
              {{ training.event.start_date.format('dddd') }}
            </td>
            <td class="py-2 px-3 text-gray-700">
              {{ training.event.start_date.format('L') }}
            </td>
            <td class="py-2 px-3 text-gray-700">
              {{ training.event.start_date.format('HH:mm') }} -
              {{ training.event.end_date.format('HH:mm') }}
            </td>
            <td class="py-2 px-3 text-gray-700">
              <template v-for="(coach, index) in training.coaches">
                <div :key="coach.id">
                  <span>{{ coach.name }}</span>
                  <span v-if="index != Object.keys(training.coaches).length - 1">,&nbsp;</span>
                </div>
              </template>
            </td>
          </tr>
        </table>
      </div>
      <div class="flex justify-end mt-6">
        <button
          class="red-button disabled:opacity-50 disabled:cursor-not-allowed"
          :disabled="!hasSelections"
          @click="submit">
          <i class="fas fa-save"></i>&nbsp; {{ $t('save') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import messages from './lang';

import Alert from '@/components/Alert';

export default {
  components: {
    Alert
  },
  props: {
    trainings: {
      type: Array,
      required: true
    }
  },
  i18n: messages,
  data() {
    return {
      selectAll: false,
      selectedTrainings: []
    };
  },
  computed: {
    count() {
      return this.trainings.length;
    },
    hasSelections() {
      return this.selectedTrainings.length > 0;
    }
  },
  watch: {
    selectAll(nv) {
      this.selectedTrainings = nv ?
        this.trainings.map((t, index) => index) : [];
    }
  },
  methods: {
    submit() {
      var selection = this.selectedTrainings.map(s => this.trainings[s]);
      this.$emit('generate', selection);
    }
  }
};
</script>
