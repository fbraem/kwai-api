import VueForm, { notEmpty, isTime } from '@/js/VueForm';

import Season from '@/models/Season';
import Team from '@/models/Team';

import moment from 'moment';

export default {
  mixins: [ VueForm ],
  form() {
    return {
      name: {
        value: '',
        required: true,
        validators: [
          {
            v: notEmpty,
            error: this.$t('training.definitions.form.name.required'),
          },
        ]
      },
      description: {
        value: '',
        required: true,
        validators: [
          {
            v: notEmpty,
            error: this.$t('training.definitions.form.description.required'),
          },
        ]
      },
      season: {
        value: 0
      },
      team: {
        value: 0
      },
      weekday: {
        value: 1,
        required: true,
        validators: [
          {
            v: (value) => value > 0,
            error: this.$t('training.definitions.form.weekday.required')
          },
        ]
      },
      start_time: {
        value: '',
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
      },
      end_time: {
        value: '',
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
      },
      active: {
        value: true
      },
      location: {
        value: null
      },
      remark: {
        value: ''
      }
    };
  },
  validations() {
    return [
      () => {
        var start = moment(this.form.start_time.value, 'HH:mm', true);
        var end = moment(this.form.end_time.value, 'HH:mm', true);
        if (end.isAfter(start)) {
          return true;
        }
        this.form.end_time.errors = [
          this.$t('training.definitions.form.end_time.after'),
        ];
        return false;
      },
    ];
  },
  methods: {
    writeForm(definition) {
      this.form.name.value = definition.name;
      this.form.description.value = definition.description;
      this.form.active.value = definition.active;
      this.form.location.value = definition.location;
      this.form.start_time.value = definition.formattedStartTime;
      this.form.end_time.value = definition.formattedEndTime;
      this.form.weekday.value = definition.weekday;
      if (definition.season) {
        this.form.season.value = definition.season.id;
      }
      if (definition.team) {
        this.form.team.value = definition.team.id;
      }
      this.form.remark.value = definition.remark;
    },
    readForm(definition) {
      definition.name = this.form.name.value;
      definition.description = this.form.description.value;
      definition.active = this.form.active.value;
      definition.weekday = this.form.weekday.value;
      definition.location = this.form.location.value;
      var tz = moment.tz.guess();
      if (this.form.start_time.value) {
        definition.start_time
          = moment(this.form.start_time.value, 'HH:mm', true);
      }
      if (this.form.end_time.value) {
        definition.end_time
          = moment(this.form.end_time.value, 'HH:mm', true);
      }
      definition.time_zone = tz;
      definition.remark = this.form.remark.value;
      if (this.form.season.value) {
        if (this.form.season.value === 0) {
          definition.season = null;
        } else {
          definition.season = new Season();
          definition.season.id = this.form.season.value;
        }
      }
      if (this.form.team.value) {
        if (this.form.team.value === 0) {
          definition.team = null;
        } else {
          definition.team = new Team();
          definition.team.id = this.form.team.value;
        }
      }
      return definition;
    }
  }
};
