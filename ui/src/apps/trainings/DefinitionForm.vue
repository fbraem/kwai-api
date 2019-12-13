<template>
  <!-- eslint-disable max-len -->
  <KwaiForm
    :form="form"
    :title="title"
    :save="$t('save')"
    :error="error"
    @submit="submit"
  >
    <KwaiFieldset :title="$t('training.definitions.title')">
      <template slot="description">
        Geef een trainingsmoment een naam en een omschrijving.
      </template>
      <div class="flex flex-wrap mb-3">
        <div class="w-full sm:w-auto sm:flex-grow sm:pl-2">
          <KwaiField
            name="name"
            :label="$t('training.definitions.form.name.label')"
          >
            <KwaiInputText :placeholder="$t('training.definitions.form.name.placeholder')" />
          </KwaiField>
        </div>
        <div class="w-full sm:w-auto sm:pl-4 self-begin flex-none">
          <KwaiField
            name="active"
            :label="$t('training.definitions.form.active.label')"
          >
            <KwaiSwitch />
          </KwaiField>
        </div>
      </div>
      <div class="sm:pl-2">
        <KwaiField
          name="description"
          :label="$t('training.definitions.form.description.label')"
        >
          <KwaiTextarea
            :rows="5"
            :placeholder="$t('training.definitions.form.description.placeholder')"
          />
        </KwaiField>
      </div>
    </KwaiFieldset>
    <KwaiFieldset title="Tijdstip">
      <template slot="description">
        Wanneer gaat dit traingsmoment door?
      </template>
      <div class="w-full sm:pl-2 mb-3">
        <KwaiField
          name="weekday"
          :label="$t('training.definitions.form.weekday.label')"
        >
          <KwaiSelect :items="weekdays" />
        </KwaiField>
      </div>
      <div class="w-full flex flex-wrap">
        <div class="w-full sm:w-1/2 sm:pl-2">
          <KwaiField
            name="start_time"
            :label="$t('training.definitions.form.start_time.label')"
          >
            <KwaiInputText :placeholder="$t('training.definitions.form.start_time.placeholder')" />
          </KwaiField>
        </div>
        <div class="w-full sm:w-1/2 sm:pl-2">
          <KwaiField
            name="end_time"
            :label="$t('training.definitions.form.end_time.label')"
          >
            <KwaiInputText :placeholder="$t('training.definitions.form.end_time.placeholder')" />
          </KwaiField>
        </div>
      </div>
    </KwaiFieldset>
    <KwaiFieldset :title="$t('training.definitions.form.season.label')">
      <template slot="description">
        Is dit trainingsmoment specifiek voor één seizoen?<br />
        Selecteer dan hier het seizoen.
      </template>
      <KwaiField
        name="season"
        :label="$t('training.definitions.form.season.label')"
      >
        <KwaiSelect :items="seasons" />
      </KwaiField>
    </KwaiFieldset>
    <KwaiFieldset :title="$t('training.definitions.form.team.label')">
      <template slot="description">
        Is dit trainingsmoment voor een specifiek team?<br />
        Selecteer dan hier het team.
      </template>
      <KwaiField
        name="team"
        :label="$t('training.definitions.form.team.label')"
      >
        <KwaiSelect :items="teams" />
      </KwaiField>
    </KwaiFieldset>
    <KwaiFieldset :title="$t('training.definitions.form.location.label')">
      <template slot="description">
        Gaat dit trainingsmoment altijd op dezelfde plaats door?<br />
        Zo ja, geef dan hier de locatie in.
      </template>
      <KwaiField
        name="location"
        :label="$t('training.definitions.form.location.label')"
        >
        <KwaiInputText :placeholder="$t('training.definitions.form.location.placeholder')" />
      </KwaiField>
    </KwaiFieldset>
    <KwaiFieldset :title="$t('training.definitions.form.remark.label')">
      <template slot="description">
        Geef eventueel nog een opmerking in over dit trainingsmoment.<br />
        Deze opmerking zal niet zichtbaar zijn voor gewone bezoekers van de
        website.
      </template>
      <KwaiField
        name="remark"
        :label="$t('training.definitions.form.remark.label')"
      >
        <KwaiTextarea
          :rows="5"
          :placeholder="$t('training.definitions.form.remark.placeholder')"
        />
      </KwaiField>
    </KwaiFieldset>
  </KwaiForm>
</template>

<script>
import moment from 'moment';

import Season from '@/models/Season';
import Team from '@/models/Team';

import KwaiForm from '@/components/forms/KwaiForm';
import KwaiFieldset from '@/components/forms/KwaiFieldset';
import KwaiField from '@/components/forms/KwaiField';
import KwaiInputText from '@/components/forms/KwaiInputText';
import KwaiTextarea from '@/components/forms/KwaiTextarea';
import KwaiSelect from '@/components/forms/KwaiSelect';
import KwaiSwitch from '@/components/forms/KwaiSwitch';

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
    KwaiForm, KwaiFieldset, KwaiField, KwaiInputText,
    KwaiTextarea, KwaiSelect, KwaiSwitch
  },
  i18n: messages,
  props: {
    creating: {
      type: Boolean
    }
  },
  data() {
    return {
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
    title() {
      return this.creating
        ? this.$t('training.definitions.create')
        : this.$t('training.definitions.update');
    },
    definition() {
      return this.$store.state.training.definition.active;
    },
    error() {
      return this.$store.state.training.definition.error;
    },
    seasons() {
      var seasons = this.$store.getters['training/season/seasonsAsOptions'];
      seasons.unshift({
        value: 0,
        text: this.$t('training.definitions.form.season.no_season')
      });
      return seasons;
    },
    teams() {
      var teams = this.$store.getters['training/team/teamsAsOptions'];
      teams.unshift({
        value: 0,
        text: this.$t('training.definitions.form.team.no_team')
      });
      return teams;
    },
  },
  async created() {
    await this.$store.dispatch('training/season/browse');
    await this.$store.dispatch('training/team/browse');
  },
  beforeRouteEnter(to, from, next) {
    next(async(vm) => {
      vm.setupForm(to.params);
      next();
    });
  },
  methods: {
    async setupForm(params) {
      if (params.id) {
        await this.$store.dispatch('training/definition/read', {
          id: params.id
        });
        this.form.writeForm(this.definition);
      } else {
        this.$store.dispatch('training/definition/create');
      }
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
