<template>
  <!-- eslint-disable max-len -->
  <div>
    <div uk-grid>
      <div class="uk-width-1-1">
        <KwaiForm :form="form" :validations="validations">
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
      <div
        uk-grid
        class="uk-width-1-1"
      >
        <div class="uk-width-expand">
        </div>
        <div class="uk-width-auto">
          <button
            class="uk-button uk-button-primary"
            :disabled="!form.$valid"
            @click="submit"
          >
            <i class="fas fa-save"></i>
            &nbsp; {{ $t('save') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment';
import Season from '@/models/Season';

import makeForm from '@/js/Form.js';
const makeSeasonForm = (fields) => {
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
  return { ...makeForm(fields), writeForm, readForm };
};

import KwaiForm from '@/components/forms/KwaiForm';
import KwaiField from '@/components/forms/KwaiField';
import KwaiInputText from '@/components/forms/KwaiInputText';
import KwaiTextarea from '@/components/forms/KwaiTextarea';
import { notEmpty, isDate } from '@/js/VueForm';

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
      }),
      validations: [
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
      ]
    };
  },
  computed: {
    creating() {
      return this.season != null && this.season.id == null;
    },
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
  watch: {
    error(nv) {
      if (nv) {
        if (nv.response.status === 422) {
          this.handleErrors(nv.response.data.errors);
        } else if (nv.response.status === 404) {
          // this.error = err.response.statusText;
        } else {
          // TODO: check if we can get here ...
          console.log(nv);
        }
      }
    }
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
