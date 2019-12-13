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
        <table class="table table-striped">
          <thead>
            <tr>
              <th>
                <input
                  type="checkbox"
                  v-model="selectAll"
                />
              </th>
              <th scope="col">
                {{ $t('training.events.day') }}
              </th>
              <th scope="col">
                {{ $t('training.events.date') }}
              </th>
              <th scope="col">
                {{ $t('training.events.time') }}
              </th>
              <th scope="col">
                {{ $t('training.events.coaches') }}
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(training, index) in trainings"
              :key="index"
            >
              <td>
                <input
                  type="checkbox"
                  v-model="selectedTrainings"
                  :value="index"
                />
              </td>
              <td>
                {{ training.event.start_date.format('dddd') }}
              </td>
              <td>
                {{ training.event.start_date.format('L') }}
              </td>
              <td>
                {{ training.event.start_date.format('HH:mm') }} -
                {{ training.event.end_date.format('HH:mm') }}
              </td>
              <td class="text-sm">
                <template v-for="(coach, index) in training.coaches">
                  <div :key="coach.id">
                    <span>{{ coach.name }}</span>
                    <span
                      v-if="index != Object.keys(training.coaches).length - 1">
                      ,&nbsp;
                    </span>
                  </div>
                </template>
              </td>
            </tr>
          </tbody>
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
