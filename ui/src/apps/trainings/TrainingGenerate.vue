<template>
  <!-- eslint-disable max-len -->
  <div class="page-container">
    <div style="grid-column: span 2;">
      <div
        v-if="count === 0"
        class="kwai-alert kwai-theme-warning"
      >
        {{ $t('training.events.no_generated_data') }}
      </div>
      <div v-else>
        <div class="kwai-alert">
          {{ $t('training.generator.help') }}
        </div>
        <div>
          <table class="kwai-table kwai-table-divider kwai-table-striped kwai-table-middle kwai-table-small">
            <tr>
              <th>
                <input
                  class="kwai-checkbox"
                  type="checkbox"
                  v-model="selectAll"
                />
              </th>
              <th>
                {{ $t('training.events.day') }}
              </th>
              <th>
                {{ $t('training.events.date') }}
              </th>
              <th>
                {{ $t('training.events.time') }}
              </th>
              <th class="kwai-table-expand">
                {{ $t('training.events.coaches') }}
              </th>
            </tr>
            <tr
              v-for="(training, index) in trainings"
              :key="index">
              <td>
                <input
                  class="kwai-checkbox"
                  type="checkbox"
                  v-model="selectedTrainings"
                  :value="index"
                />
              </td>
              <td>
                {{ training.event.start_date.format('dddd') }}
              </td>
              <td>
                {{ training.event.start_date.format('L HH:mm') }}
              </td>
              <td>
                {{ training.event.end_date.format('L HH:mm') }}
              </td>
              <td>
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
        <div style="display:flex; justify-content: flex-end;">
          <button
            class="kwai-button kwai-theme-primary"
            :disabled="!hasSelections"
            @click="submit">
            <i class="fas fa-save"></i>&nbsp; {{ $t('save') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import messages from './lang';

export default {
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
