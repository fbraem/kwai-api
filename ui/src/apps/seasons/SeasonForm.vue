<template>
  <!-- eslint-disable max-len -->
  <div class="m-4">
    <KwaiForm
      :form="form"
      :error="error"
      :save="$t('save')"
      @submit="submit"
    >
      <KwaiField
        name="name"
        :label="$t('form.season.name.label')"
      >
        <KwaiInputText :placeholder="$t('form.season.name.placeholder')" />
      </KwaiField>
      <KwaiField
        name="start_date"
        :label="$t('form.season.start_date.label')"
      >
        <KwaiInputText :placeholder="$t('form.season.start_date.placeholder')" />
      </KwaiField>
      <KwaiField
        name="end_date"
        :label="$t('form.season.end_date.label')"
      >
        <KwaiInputText :placeholder="$t('form.season.end_date.placeholder')" />
      </KwaiField>
      <KwaiField
        name="remark"
        :label="$t('form.season.remark.label')"
      >
        <KwaiTextarea :placeholder="$t('form.season.remark.placeholder')" />
      </KwaiField>
    </KwaiForm>
  </div>
</template>

<script>
import moment from 'moment';
import Season from '@/models/Season';

import makeForm, { makeField, notEmpty, isDate } from '@/js/Form.js';
const makeSeasonForm = (fields, validations) => {
  const writeForm = (season) => {
    fields.name.value = season.name;
    fields.start_date.value = season.formatted_start_date;
    fields.end_date.value = season.formatted_end_date;
    fields.remark.value = season.remark;
  };
  const readForm = (season) => {
    season.name = fields.name.value;
    season.start_date = moment(fields.start_date.value, 'L');
    season.end_date = moment(fields.end_date.value, 'L');
    season.remark = fields.remark.value;
  };
  return { ...makeForm(fields, validations), writeForm, readForm };
};

import KwaiForm from '@/components/forms/KwaiForm';
import KwaiField from '@/components/forms/KwaiField';
import KwaiInputText from '@/components/forms/KwaiInputText';
import KwaiTextarea from '@/components/forms/KwaiTextarea';

import messages from './lang';

export default {
  i18n: messages,
  components: {
    KwaiForm,
    KwaiField,
    KwaiInputText,
    KwaiTextarea
  },
  data() {
    return {
      season: new Season(),
      form: makeSeasonForm({
        name: makeField({
          required: true,
          value: '',
          validators: [
            {
              v: notEmpty,
              error: this.$t('form.season.name.required'),
            },
          ]
        }),
        start_date: makeField({
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
        }),
        end_date: makeField({
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
        }),
        remark: makeField({
          value: ''
        })
      },
      [
        ({start_date, end_date}) => {
          var start = moment(start_date.value, 'L', true);
          var end = moment(end_date.value, 'L', true);
          if (end.isAfter(start)) {
            end_date.errors.splice(0, end_date.errors.length);
            return true;
          }
          end_date.errors.push(this.$t('form.season.end_date.after'));
          return false;
        },
      ]),
    };
  },
  computed: {
    error() {
      return this.$store.state.season.error;
    },
  },
  beforeRouteEnter(to, from, next) {
    next(async(vm) => {
      if (to.params.id) await vm.fetchData(to.params);
      next();
    });
  },
  async beforeRouteUpdate(to, from, next) {
    if (to.params.id) await this.fetchData(to.params);
    next();
  },
  methods: {
    async fetchData(params) {
      this.season = await this.$store.dispatch('season/read', {
        id: params.id
      });
      this.form.writeForm(this.season);
    },
    async submit() {
      this.form.clearErrors();
      this.form.readForm(this.season);
      try {
        this.season = await this.$store.dispatch('season/save', this.season);
        this.$router.push({
          name: 'seasons.read',
          params: {
            id: this.season.id
          }
        });
      } catch (error) {
        console.log(error);
      };
    }
  }
};
</script>
