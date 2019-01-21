import VueForm, { isDate, notEmpty } from '@/js/VueForm';
import moment from 'moment';

/**
 * Mixin for the Content form.
 */
export default {
  mixins: [ VueForm ],
  form() {
    return {
      name: {
        required: true,
        value: '',
        validators: [
          {
            v: notEmpty,
            error: this.$t('form.season.name.required'),
          },
        ]
      },
      start_date: {
        required: true,
        value: moment().format('L'),
        validators: [
          {
            v: notEmpty,
            error: this.$t('form.season.start_date.required'),
          },
          {
            v: isDate,
            error: this.$t('form.season.start_date.invalid', {
              format: moment.localeData().longDateFormat('L')
            }),
          },
        ]
      },
      end_date: {
        required: true,
        value: '',
        validators: [
          {
            v: notEmpty,
            error: this.$t('form.season.end_date.required'),
          },
          {
            v: isDate,
            error: this.$t('form.season.end_date.invalid', {
              format: moment.localeData().longDateFormat('L')
            }),
          },
        ]
      },
      remark: {
        value: ''
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
          this.$t('form.season.end_date.after'),
        ];
        return false;
      },
    ];
  },
  methods: {
    writeForm(season) {
      this.form.name.value = season.name;
      this.form.start_date.value = season.formatted_start_date;
      this.form.end_date.value = season.formatted_end_date;
      this.form.remark.value = season.remark;
    },
    readForm(season) {
      season.name = this.form.name.value;
      season.start_date = moment(this.form.start_date.value, 'L');
      season.end_date = moment(this.form.end_date.value, 'L');
      season.remark = this.form.remark.value;
    }
  }
};
