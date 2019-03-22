<template>
  <!-- eslint-disable max-len -->
  <div uk-grid>
    <div class="uk-width-1-1">
      <KwaiForm
        :form="form"
        :error="error"
        :save="$t('save')"
        @submit="submit"
      >
        <div uk-grid>
          <div class="uk-width-expand">
            <KwaiField
              name="title"
              :label="$t('training.events.form.title.label')"
            >
              <KwaiInputText :placeholder="$t('training.events.form.title.placeholder')" />
            </KwaiField>
          </div>
          <div>
            <KwaiField
              name="active"
              :label="$t('training.events.form.active.label')"
            >
              <KwaiSwitch />
            </KwaiField>
          </div>
          <div>
            <KwaiField
              name="cancelled"
              :label="$t('training.events.form.cancelled.label')"
            >
              <KwaiSwitch />
            </KwaiField>
          </div>
        </div>
        <KwaiField
          name="summary"
          :label="$t('training.events.form.summary.label')"
        >
          <KwaiTextarea
            :rows="5"
            :placeholder="$t('training.events.form.summary.placeholder')"
          />
        </KwaiField>
        <div class="uk-child-width-1-3" uk-grid>
          <div>
            <KwaiField
              name="start_date"
              :label="$t('training.events.form.start_date.label')"
            >
              <KwaiInputText :placeholder="$t('training.events.form.start_date.placeholder')" />
            </KwaiField>
          </div>
          <div>
            <KwaiField
              name="start_time"
              :label="$t('training.events.form.start_time.label')"
            >
              <KwaiInputText :placeholder="$t('training.events.form.start_time.placeholder')" />
            </KwaiField>
          </div>
          <div>
            <KwaiField
              name="end_time"
              :label="$t('training.events.form.end_time.label')"
            >
              <KwaiInputText :placeholder="$t('training.events.form.end_time.placeholder')" />
            </KwaiField>
          </div>
        </div>
        <KwaiField
          name="season"
          :label="$t('training.events.form.season.label')"
        >
          <KwaiSelect :items="seasons" />
        </KwaiField>
        <KwaiField
          name="teams"
          :label="$t('training.events.form.teams.label')"
        >
          <Multiselect
            :options="teams"
            label="name"
            track-by="id"
            :multiple="true"
            :close-on-select="false"
            :selectLabel="$t('training.events.form.teams.selectLabel')"
            :deselectLabel="$t('training.events.form.teams.deselectLabel')"
          />
        </KwaiField>
        <KwaiField
          name="coaches"
          :label="$t('training.events.form.coaches.label')"
        >
          <Multiselect
            :options="coaches"
            label="name"
            track-by="id"
            :multiple="true"
            :close-on-select="false"
            :selectLabel="$t('training.events.form.coaches.selectLabel')"
            :deselectLabel="$t('training.events.form.coaches.deselectLabel')"
          />
        </KwaiField>
        <KwaiField
          name="remark"
          :label="$t('training.events.form.remark.label')"
        >
          <KwaiTextarea
            :rows="5"
            :placeholder="$t('training.events.form.remark.placeholder')"
          />
        </KwaiField>
      </KwaiForm>
    </div>
  </div>
</template>

<script>
import moment from 'moment';

import Training from '@/models/trainings/Training';
import Season from '@/models/Season';

import makeForm, { makeField, notEmpty, isTime, isDate } from '@/js/Form';
const makeTrainingForm = (fields, validations) => {
  const writeForm = (training) => {
    fields.title.value = training.content.title;
    fields.summary.value = training.content.summary;
    fields.active.value = training.event.active;
    fields.cancelled.value = training.event.cancelled;
    fields.location.value = training.event.location;
    fields.start_date.value = training.formattedStartDate;
    fields.start_time.value = training.formattedStartTime;
    fields.end_time.value = training.formattedEndTime;
    fields.coaches.value = training.coaches;
    if (training.season) {
      fields.season.value = training.season.id;
    }
    fields.teams.value = training.teams;
    fields.remark.value = training.event.remark;
  };

  const readForm = (training) => {
    if (!training.event) {
      training.event = Object.create(null);
      training.event.contents = [ Object.create(null) ];
    }
    training.event.contents[0].title = fields.title.value;
    training.event.contents[0].summary = fields.summary.value;
    training.event.active = fields.active.value;
    training.event.cancelled = fields.cancelled.value;
    training.event.location = fields.location.value;
    training.event.time_zone = moment.tz.guess();
    const date = moment(fields.start_date.value, 'L', true);
    const startTime = moment(fields.start_time.value, 'HH:mm', true);
    training.event.start_date = date.clone();
    training.event.start_date.hours(startTime.hours());
    training.event.start_date.minutes(startTime.minutes());
    training.event.end_date = date.clone();
    const endTime = moment(fields.end_time.value, 'HH:mm', true);
    training.event.end_date.hours(endTime.hours());
    training.event.end_date.minutes(endTime.minutes());
    training.event.remark = fields.remark.value;
    training.coaches = fields.coaches.value;
    training.teams = fields.teams.value;
    if (fields.season.value) {
      if (fields.season.value === 0) {
        training.season = null;
      } else {
        training.season = new Season();
        training.season.id = fields.season.value;
      }
    }
  };
  return { ...makeForm(fields, validations), writeForm, readForm };
};

import KwaiForm from '@/components/forms/KwaiForm.vue';
import KwaiField from '@/components/forms/KwaiField.vue';
import KwaiInputText from '@/components/forms/KwaiInputText.vue';
import KwaiTextarea from '@/components/forms/KwaiTextarea.vue';
import KwaiSelect from '@/components/forms/KwaiSelect.vue';
import KwaiSwitch from '@/components/forms/KwaiSwitch.vue';
import Multiselect from '@/components/forms/MultiSelect.vue';

import messages from './lang';

export default {
  components: {
    KwaiForm,
    KwaiField,
    KwaiInputText,
    KwaiSwitch,
    KwaiSelect,
    KwaiTextarea,
    Multiselect
  },
  i18n: messages,
  data() {
    return {
      training: new Training(),
      form: makeTrainingForm({
        title: makeField({
          required: true,
          validators: [
            {
              v: notEmpty,
              error: this.$t('training.events.form.title.required'),
            },
          ]
        }),
        summary: makeField({
          required: true,
          validators: [
            {
              v: notEmpty,
              error: this.$t('training.events.form.summary.required'),
            },
          ]
        }),
        season: makeField({
          value: 0
        }),
        coaches: makeField({
          value: []
        }),
        teams: makeField({
          value: []
        }),
        start_date: makeField({
          required: true,
          validators: [
            {
              v: notEmpty,
              error: this.$t('training.events.form.start_date.required')
            },
            {
              v: isDate,
              error: this.$t('training.events.form.start_date.invalid')
            },
          ]
        }),
        start_time: makeField({
          required: true,
          validators: [
            {
              v: notEmpty,
              error: this.$t('training.events.form.start_time.required')
            },
            {
              v: isTime,
              error: this.$t('training.events.form.start_time.invalid')
            },
          ]
        }),
        end_time: makeField({
          required: true,
          validators: [
            {
              v: notEmpty,
              error: this.$t('training.events.form.end_time.required')
            },
            {
              v: isTime,
              error: this.$t('training.events.form.end_time.invalid')
            },
          ]
        }),
        active: makeField({
          value: true
        }),
        cancelled: makeField({
          value: false
        }),
        location: makeField(),
        remark: makeField()
      },
      [
        ({start_time, end_time}) => {
          var start = moment(start_time.value, 'HH:mm', true);
          var end = moment(end_time.value, 'HH:mm', true);
          if (end.isAfter(start)) {
            return true;
          }
          end_time.errors = [
            this.$t('training.events.form.end_time.after'),
          ];
          return false;
        },
      ])
    };
  },
  computed: {
    error() {
      return this.$store.state.training.error;
    },
    seasons() {
      var seasons = this.$store.getters['season/seasonsAsOptions'];
      seasons.unshift({
        value: 0,
        text: this.$t('training.events.form.season.no_season')
      });
      return seasons;
    },
    coaches() {
      return this.$store.state.training.coach.coaches || [];
    },
    teams() {
      return this.$store.state.team.teams || [];
    },
  },
  async created() {
    await this.$store.dispatch('season/browse');
    await this.$store.dispatch('training/coach/browse');
    await this.$store.dispatch('team/browse');
  },
  beforeRouteEnter(to, from, next) {
    next(async(vm) => {
      if (to.params.id) await vm.fetchData(to.params.id);
      next();
    });
  },
  watch: {
    error(nv) {
      if (nv) {
        if (nv.response.status === 422) {
          this.handleErrors(nv.response.data.errors);
        } else if (nv.response.status === 404){
          // this.error = err.response.statusText;
        } else {
          // TODO: check if we can get here ...
          console.log(nv);
        }
      }
    }
  },
  methods: {
    async fetchData(id) {
      this.training
        = await this.$store.dispatch('training/read', {
          id: id
        });
      this.form.writeForm(this.training);
    },
    submit() {
      this.form.clearErrors();
      this.form.readForm(this.training);
      this.$store.dispatch('training/save', this.training)
        .then((newTraining) => {
          this.$router.push({
            name: 'trainings.read',
            params: { id: newTraining.id }
          });
        });
    }
  }
};
</script>
