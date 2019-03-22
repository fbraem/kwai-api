<template>
  <!-- eslint-disable max-len -->
  <section class="uk-section uk-section-small uk-container uk-container-expand">
    <div uk-grid>
      <div
        v-if="count === 0"
        class="uk-width-1-1"
      >
        <div class="uk-alert uk-alert-warning">
          {{ $t('training.events.no_generated_data') }}
        </div>
      </div>
      <div
        v-else
        uk-grid
      >
        <div class="uk-width-1-1">
          <div uk-alert>
            {{ $t('training.generator.help') }}
          </div>
        </div>
        <div class="uk-width-1-1">
          <table class="uk-table uk-table-divider uk-table-striped uk-table-middle uk-table-small">
            <tr>
              <th>
                <input
                  class="uk-checkbox"
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
              <th class="uk-table-expand">
                {{ $t('training.events.coaches') }}
              </th>
            </tr>
            <tr
              v-for="(training, index) in trainings"
              :key="index"
              :class="{ 'uk-text-muted': training.disabled }"
              :style="{'text-decoration': training.disabled ? 'line-through' : 'none'}">
              <td>
                <input
                  class="uk-checkbox"
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
        <div uk-grid>
          <div class="uk-width-expand">
          </div>
          <div class="uk-width-auto">
            <button
              class="uk-button uk-button-primary"
              :disabled="!hasSelections"
              @click="submit">
              <i class="fas fa-save"></i>&nbsp; {{ $t('save') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>
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
