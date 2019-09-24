<template>
  <!-- eslint-disable max-len -->
  <div class="page-container">
    <KwaiForm
      :form="form"
      :save="$t('save')"
      :error="error"
      @submit="submit"
      style="grid-column: span 2;"
    >
      <div style="display:flex;">
        <div style="flex-grow:1;">
          <KwaiField
            name="name"
            :label="$t('training.definitions.form.name.label')"
          >
            <KwaiInputText :placeholder="$t('training.definitions.form.name.placeholder')" />
          </KwaiField>
        </div>
        <div style="align-self:flex-end;margin-left: 20px;">
          <KwaiField
            name="active"
            :label="$t('training.definitions.form.active.label')"
          >
            <KwaiSwitch />
          </KwaiField>
        </div>
      </div>
      <KwaiField
        name="description"
        :label="$t('training.definitions.form.description.label')"
      >
        <KwaiTextarea
          :rows="5"
          :placeholder="$t('training.definitions.form.description.placeholder')"
        />
      </KwaiField>
      <KwaiField
        name="weekday"
        :label="$t('training.definitions.form.weekday.label')"
      >
        <KwaiSelect :items="weekdays" />
      </KwaiField>
      <div
        class="uk-child-width-1-2"
        uk-grid
      >
        <div>
          <KwaiField
            name="start_time"
            :label="$t('training.definitions.form.start_time.label')"
          >
            <KwaiInputText :placeholder="$t('training.definitions.form.start_time.placeholder')" />
          </KwaiField>
        </div>
        <div>
          <KwaiField
            name="end_time"
            :label="$t('training.definitions.form.end_time.label')"
          >
            <KwaiInputText :placeholder="$t('training.definitions.form.end_time.placeholder')" />
          </KwaiField>
        </div>
      </div>
      <KwaiField
        name="season"
        :label="$t('training.definitions.form.season.label')"
      >
        <KwaiSelect :items="seasons" />
      </KwaiField>
      <KwaiField
        name="team"
        :label="$t('training.definitions.form.team.label')"
      >
        <KwaiSelect :items="teams" />
      </KwaiField>
      <KwaiField
        name="location"
        :label="$t('training.definitions.form.location.label')"
      >
        <KwaiInputText :placeholder="$t('training.definitions.form.location.placeholder')" />
      </KwaiField>
      <KwaiField
        name="remark"
        :label="$t('training.definitions.form.remark.label')"
      >
        <KwaiTextarea
          :rows="5"
          :placeholder="$t('training.definitions.form.remark.placeholder')"
        />
      </KwaiField>
    </KwaiForm>
  </div>
</template>

<script>
import moment from 'moment';

import TrainingDefinition from '@/models/trainings/Definition';
import Season from '@/models/Season';
import Team from '@/models/Team';

import KwaiForm from '@/components/forms/KwaiForm';
import KwaiField from '@/components/forms/KwaiField.vue';
import KwaiInputText from '@/components/forms/KwaiInputText.vue';
import KwaiTextarea from '@/components/forms/KwaiTextarea.vue';
import KwaiSelect from '@/components/forms/KwaiSelect.vue';
import KwaiSwitch from '@/components/forms/KwaiSwitch.vue';

import makeForm, { makeField, notEmpty, isTime } from '@/js/Form';
const makeDefinitionForm = (fields, validations) => {
  const writeForm = (definition) => {
    fields.name.value = definition.name;
    fields.description.value = definition.description;
    fields.active.value = definition.active;
    fields.location.value = definition.location;
    fields.start_time.value = definition.formattedStartTime;
    fields.end_time.value = definition.formattedEndTime;
    fields.weekday.value = definition.weekday;
    if (definition.season) {
      fields.season.value = definition.season.id;
    }
    if (definition.team) {
      fields.team.value = definition.team.id;
    }
    fields.remark.value = definition.remark;
  };

  const readForm = (definition) => {
    definition.name = fields.name.value;
    definition.description = fields.description.value;
    definition.active = fields.active.value;
    definition.weekday = fields.weekday.value;
    definition.location = fields.location.value;
    var tz = moment.tz.guess();
    if (fields.start_time.value) {
      definition.start_time
        = moment(fields.start_time.value, 'HH:mm', true);
    }
    if (fields.end_time.value) {
      definition.end_time
        = moment(fields.end_time.value, 'HH:mm', true);
    }
    definition.time_zone = tz;
    definition.remark = fields.remark.value;
    if (fields.season.value) {
      if (fields.season.value === 0) {
        definition.season = null;
      } else {
        definition.season = new Season();
        definition.season.id = fields.season.value;
      }
    }
    if (fields.team.value) {
      if (fields.team.value === 0) {
        definition.team = null;
      } else {
        definition.team = new Team();
        definition.team.id = fields.team.value;
      }
    }
  };

  return { ...makeForm(fields, validations), writeForm, readForm };
};

import messages from './lang';

export default {
  components: {
    KwaiForm, KwaiField, KwaiInputText, KwaiTextarea,
    KwaiSelect, KwaiSwitch
  },
  i18n: messages,
  data() {
    return {
      definition: new TrainingDefinition(),
      weekdays: moment.weekdays(true).map((d, i) => {
        return {
          value: i + 1,
          text: d
        };
      }),
      form: makeDefinitionForm({
        name: makeField({
          required: true,
          validators: [
            {
              v: notEmpty,
              error: this.$t('training.definitions.form.name.required'),
            },
          ]
        }),
        description: makeField({
          required: true,
          validators: [
            {
              v: notEmpty,
              error: this.$t('training.definitions.form.description.required'),
            },
          ]
        }),
        season: makeField({
          value: 0
        }),
        team: makeField({
          value: 0
        }),
        weekday: makeField({
          value: 1,
          required: true,
          validators: [
            {
              v: (value) => value > 0,
              error: this.$t('training.definitions.form.weekday.required')
            },
          ]
        }),
        start_time: makeField({
          required: true,
          validators: [
            {
              v: notEmpty,
              error: this.$t('training.definitions.form.start_time.required')
            },
            {
              v: isTime,
              error: this.$t('training.definitions.form.start_time.invalid')
            },
          ]
        }),
        end_time: makeField({
          required: true,
          validators: [
            {
              v: notEmpty,
              error: this.$t('training.definitions.form.end_time.required')
            },
            {
              v: isTime,
              error: this.$t('training.definitions.form.end_time.invalid')
            },
          ]
        }),
        active: makeField({
          value: true
        }),
        location: makeField(),
        remark: makeField()
      })
    };
  },
  computed: {
    creating() {
      return this.definition != null && this.definition.id == null;
    },
    error() {
      return this.$store.state.training.definition.error;
    },
    seasons() {
      var seasons = this.$store.getters['season/seasonsAsOptions'];
      seasons.unshift({
        value: 0,
        text: this.$t('training.definitions.form.season.no_season')
      });
      return seasons;
    },
    teams() {
      var teams = this.$store.getters['team/teamsAsOptions'];
      teams.unshift({
        value: 0,
        text: this.$t('training.definitions.form.team.no_team')
      });
      return teams;
    },
  },
  async created() {
    await this.$store.dispatch('season/browse');
    await this.$store.dispatch('team/browse');
  },
  beforeRouteEnter(to, from, next) {
    next(async(vm) => {
      if (to.params.id) await vm.fetchData(to.params.id);
      next();
    });
  },
  methods: {
    clear() {
      // this.$v.$reset();
      // this.form.reset();
    },
    async fetchData(id) {
      this.definition
        = await this.$store.dispatch('training/definition/read', {
          id: id
        });
      this.form.writeForm(this.definition);
    },
    submit() {
      this.form.clearErrors();
      this.form.readForm(this.definition);
      this.$store.dispatch('training/definition/save', this.definition)
        .then((newDefinition) => {
          this.$router.push({
            name: 'trainings.definitions.read',
            params: { id: newDefinition.id }
          });
        });
    }
  }
};
</script>
