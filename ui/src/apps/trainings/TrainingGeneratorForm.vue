<template>
  <!-- eslint-disable max-len -->
  <div class="uk-child-width-1-1" uk-grid>
    <div>
      <div class="uk-width-auto">
        <button class="uk-button" uk-toggle="target: #events">
          <i class="far fa-calendar-alt"></i>&nbsp; {{ $t('trainings') }}
        </button>
      </div>
      <div class="uk-width-expand">
      </div>
    </div>
    <div id="events" hidden>
      <p>
        {{ $t('training.generator.create') }}
      </p>
      <form class="uk-form-stacked">
        <div class="uk-child-width-1-2" uk-grid>
          <div>
            <field name="start_date">
              <uikit-input-text :placeholder="$t('training.generator.form.start_date.placeholder')" />
            </field>
          </div>
          <div>
            <field name="end_date">
              <uikit-input-text :placeholder="$t('training.generator.form.end_date.placeholder')" />
            </field>
          </div>
        </div>
        <field name="coaches">
          <multiselect :options="coaches" label="name"
            track-by="id" :multiple="true" :close-on-select="false"
            :selectLabel="$t('training.generator.form.coaches.selectLabel')"
            :deselectLabel="$t('training.generator.form.coaches.deselectLabel')">
          </multiselect>
        </field>
        <div uk-grid class="uk-width-1-1">
          <div class="uk-width-expand">
          </div>
          <div class="uk-width-auto">
            <button class="uk-button uk-button-primary" :disabled="!$valid" @click="generate">
              <i class="fas fa-save"></i>&nbsp; {{ $t('training.generator.form.generate') }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import moment from 'moment';

import messages from './lang';

import trainingStore from '@/stores/training';
import definitionStore from '@/stores/training/definitions';
import coachStore from '@/stores/training/coaches';
import registerModule from '@/stores/mixin';

import Field from '@/components/forms/Field.vue';
import UikitInputText from '@/components/forms/InputText.vue';
import Multiselect from '@/components/forms/MultiSelect.vue';

import TrainingGeneratorForm from './TrainingGeneratorForm';

export default {
  props: [
    'definition',
  ],
  components: {
    Multiselect, Field, UikitInputText
  },
  i18n: messages,
  mixins: [
    TrainingGeneratorForm,
    registerModule(
      {
        training: trainingStore
      },
      {
        training: trainingStore,
        definition: definitionStore
      },
      {
        training: trainingStore,
        coach: coachStore,
      }
    ),
  ],
  computed: {
    error() {
      return this.$store.state.training.definition.error;
    },
    coaches() {
      var coaches = this.$store.state.training.coach.coaches;
      return coaches || [];
    }
  },
  mounted() {
    this.fetchCoaches();
  },
  methods: {
    fetchCoaches() {
      this.$store.dispatch('training/coach/browse').catch((err) => {
        console.log(err);
      });
    },
    generate() {
      this.$store.dispatch('training/event/generate', {
        start: moment(this.form.start_date.value, 'L'),
        end: moment(this.form.end_date.value, 'L'),
        definition: this.definition,
        coaches: this.form.coaches.value
      });
      this.$router.push({
        name: 'trainings.events.generate'
      });
    }
  }
};
</script>
