<template>
  <!-- eslint-disable max-len -->
  <div uk-grid>
    <div class="uk-width-1-1">
      <div class="uk-width-auto">
        <button
          class="uk-button"
          uk-toggle="target: #events"
        >
          <i class="far fa-calendar-alt"></i>&nbsp; {{ $t('trainings') }}
        </button>
      </div>
      <div class="uk-width-expand">
      </div>
    </div>
    <div
      class="uk-width-1-1"
      id="events"
      hidden
    >
      <p>
        {{ $t('training.generator.create') }}
      </p>
      <KwaiForm
        :form="form"
        @submit="generate"
        :save="$t('training.generator.form.generate')"
      >
        <div class="uk-child-width-1-2" uk-grid>
          <div>
            <KwaiField name="start_date">
              <KwaiInputText :placeholder="$t('training.generator.form.start_date.placeholder')" />
            </KwaiField>
          </div>
          <div>
            <KwaiField name="end_date">
              <KwaiInputText :placeholder="$t('training.generator.form.end_date.placeholder')" />
            </KwaiField>
          </div>
        </div>
        <KwaiField name="coaches">
          <multiselect
            :options="coaches"
            label="name"
            track-by="id"
            :multiple="true"
            :close-on-select="false"
            :selectLabel="$t('training.generator.form.coaches.selectLabel')"
            :deselectLabel="$t('training.generator.form.coaches.deselectLabel')"
          />
        </KwaiField>
      </KwaiForm>
      <EventGenerate
        v-if="trainings"
        :trainings="trainings"
        @generate="createAll"
      />
    </div>
    <notifications position="bottom right" />
  </div>
</template>

<script>
import moment from 'moment';

import messages from './lang';

import Training from '@/models/trainings/Training';

import KwaiForm from '@/components/forms/KwaiForm';
import KwaiField from '@/components/forms/KwaiField.vue';
import KwaiInputText from '@/components/forms/KwaiInputText.vue';
import Multiselect from '@/components/forms/MultiSelect.vue';

import EventGenerate from './TrainingGenerate.vue';

import makeForm, { makeField, notEmpty, isDate } from '@/js/Form';

export default {
  props: [
    'definition',
  ],
  components: {
    KwaiForm, Multiselect, KwaiField, KwaiInputText, EventGenerate
  },
  i18n: messages,
  data() {
    return {
      trainings: null,
      form: makeForm({
        start_date: makeField({
          value: moment().format('L'),
          label: this.$t('training.generator.form.start_date.label'),
          required: true,
          validators: [
            {
              v: notEmpty,
              error: this.$t('training.generator.form.start_date.required')
            },
            {
              v: isDate,
              error: this.$t('training.generator.form.start_date.invalid', {
                format: moment.localeData().longDateFormat('L')
              })
            },
          ]
        }),
        end_date: makeField({
          required: true,
          label: this.$t('training.generator.form.end_date.label'),
          validators: [
            {
              v: notEmpty,
              error: this.$t('training.generator.form.end_date.required')
            },
            {
              v: isDate,
              error: this.$t('training.generator.form.end_date.invalid', {
                format: moment.localeData().longDateFormat('L')
              })
            },
          ]
        }),
        coaches: makeField({
          value: [],
          label: this.$t('training.generator.form.coaches.label'),
          placeholder: this.$t('training.generator.form.coaches.placeholder')
        })
      })
    };
  },
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
      var tz = moment.tz.guess();
      var start = moment(this.form.fields.start_date.value, 'L');
      var end = moment(this.form.fields.end_date.value, 'L');
      var next = start.day(this.definition.weekday);
      var trainings = [];
      while (next.isBefore(end)) {
        var training = new Training();
        training.event = Object.create(null);
        var content = Object.create(null);
        content.title = this.definition.name;
        content.summary = this.definition.description;
        training.event.contents = [ content ];
        var s = next.clone();
        s.hours(this.definition.start_time.hours());
        s.minutes(this.definition.start_time.minutes());
        training.event.start_date = s;

        var e = next.clone();
        e.hours(this.definition.end_time.hours());
        e.minutes(this.definition.end_time.minutes());
        training.event.end_date = e;

        training.event.location = this.definition.location;
        training.event.time_zone = tz;
        training.definition = this.definition;
        // TODO: Make it possible to assign multiple teams to a definition
        if (this.definition.team) {
          training.teams = [ this.definition.team ];
        }
        training.coaches = this.form.fields.coaches.value;
        trainings.push(training);
        next = next.day(this.definition.weekday + 7);
      }
      this.trainings = trainings;
    },
    createAll(selection) {
      this.$store.dispatch('training/createAll', selection).then(() => {
        this.trainings = null;
        this.$notify({
          type: 'success',
          title: 'Success!',
          text: 'All trainings were created!'
        });
      });
    }
  }
};
</script>
