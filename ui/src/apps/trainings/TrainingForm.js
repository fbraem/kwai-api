import VueForm, { notEmpty, isDate, isTime } from '@/js/VueForm';

import Season from '@/models/Season';

import moment from 'moment';

export default {
  mixins: [ VueForm ],
  form() {
    return {
      title: {
        value: '',
        required: true,
        validators: [
          {
            v: notEmpty,
            error: this.$t('training.events.form.title.required'),
          },
        ]
      },
      summary: {
        value: '',
        required: true,
        validators: [
          {
            v: notEmpty,
            error: this.$t('training.events.form.summary.required'),
          },
        ]
      },
      season: {
        value: 0
      },
      coaches: {
        value: []
      },
      teams: {
        value: []
      },
      start_date: {
        value: '',
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
      },
      start_time: {
        value: '',
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
      },
      end_time: {
        value: '',
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
      },
      active: {
        value: true
      },
      cancelled: {
        value: false
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
          this.$t('training.events.form.end_time.after'),
        ];
        return false;
      },
    ];
  },
  methods: {
    writeForm(training) {
      this.form.title.value = training.content.title;
      this.form.summary.value = training.content.summary;
      this.form.active.value = training.event.active;
      this.form.cancelled.value = training.event.cancelled;
      this.form.location.value = training.event.location;
      this.form.start_date.value = training.formattedStartDate;
      this.form.start_time.value = training.formattedStartTime;
      this.form.end_time.value = training.formattedEndTime;
      this.form.coaches.value = training.coaches;
      if (training.season) {
        this.form.season.value = training.season.id;
      }
      this.form.teams.value = training.teams;
      this.form.remark.value = training.event.remark;
    },
    readForm(training) {
      if (!training.event) {
        training.event = Object.create(null);
        training.event.contents = [ Object.create(null) ];
      }
      training.event.contents[0].title = this.form.title.value;
      training.event.contents[0].summary = this.form.summary.value;
      training.event.active = this.form.active.value;
      training.event.cancelled = this.form.cancelled.value;
      training.event.location = this.form.location.value;
      training.event.time_zone = moment.tz.guess();
      const date = moment(this.form.start_date.value, 'L', true);
      const startTime = moment(this.form.start_time.value, 'HH:mm', true);
      training.event.start_date = date.clone();
      training.event.start_date.hours(startTime.hours());
      training.event.start_date.minutes(startTime.minutes());
      training.event.end_date = date.clone();
      const endTime = moment(this.form.end_time.value, 'HH:mm', true);
      training.event.end_date.hours(endTime.hours());
      training.event.end_date.minutes(endTime.minutes());
      training.event.remark = this.form.remark.value;
      training.coaches = this.form.coaches.value;
      training.teams = this.form.teams.value;
      if (this.form.season.value) {
        if (this.form.season.value === 0) {
          training.season = null;
        } else {
          training.season = new Season();
          training.season.id = this.form.season.value;
        }
      }
      return training;
    }
  }
};
