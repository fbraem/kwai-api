import VueForm, { notEmpty, isDate } from '@/js/VueForm';

import moment from 'moment';

export default {
  mixins: [ VueForm ],
  form() {
    return {
      start_date: {
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
      },
      end_date: {
        value: '',
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
      },
      coaches: {
        value: [],
        label: this.$t('training.generator.form.coaches.label'),
        placeholder: this.$t('training.generator.form.coaches.placeholder')
      }
    };
  },
  validations() {
    return [
      () => {
        var start = moment(this.form.start_date.value, 'L', true);
        var end = moment(this.form.end_date.value, 'L', true);
        if (end.isAfter(start)) {
          return true;
        }
        this.form.end_date.errors = [
          this.$t('training.generator.form.end_date.after'),
        ];
        return false;
      },
    ];
  },
  methods: {
    writeForm(definition) {
    },
    readForm(definition) {
    }
  }
};
