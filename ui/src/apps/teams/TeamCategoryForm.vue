<template>
  <!-- eslint-disable max-len -->
  <div class="m-6">
    <KwaiForm
      :form="form"
      :save="$t('save')"
      @submit="submit"
    >
      <KwaiField
        name="name"
        :label="$t('form.team_category.name.label')"
      >
        <KwaiInputText :placeholder="$t('form.team_category.name.placeholder')" />
      </KwaiField>
      <KwaiField
        name="start_age"
        :label="$t('form.team_category.start_age.label')"
      >
        <KwaiInputText :placeholder="$t('form.team_category.start_age.placeholder')" />
      </KwaiField>
      <KwaiField
        name="end_age"
        :label="$t('form.team_category.end_age.label')"
      >
        <KwaiInputText :placeholder="$t('form.team_category.end_age.placeholder')" />
      </KwaiField>
      <KwaiField
        name="gender"
        :label="$t('form.team_category.gender.label')"
      >
        <KwaiSelect :items="genders" />
      </KwaiField>
      <KwaiField
        name="active"
        :label="$t('form.team_category.active.label')"
      >
        <KwaiCheckbox />
      </KwaiField>
      <KwaiField
        name="competition"
        :label="$t('form.team_category.competition.label')"
      >
        <KwaiCheckbox />
      </KwaiField>
      <KwaiField
        name="remark"
        :label="$t('form.team_category.remark.label')"
      >
        <KwaiTextarea
          :placeholder="$t('form.team_category.remark.placeholder')"
          :rows="5"
        />
      </KwaiField>
    </KwaiForm>
  </div>
</template>

<script>
import TeamCategory from '@/models/TeamCategory';

import makeForm, { makeField, notEmpty, isInteger } from '@/js/Form';
const makeTeamTypeForm = (fields) => {
  const writeForm = (teamCategory) => {
    fields.name.value = teamCategory.name;
    fields.start_age.value = teamCategory.start_age;
    fields.end_age.value = teamCategory.end_age;
    fields.gender.value = teamCategory.gender;
    fields.active.value = teamCategory.active;
    fields.competition.value = teamCategory.competition;
    fields.remark.value = teamCategory.remark;
  };
  const readForm = (teamCategory) => {
    teamCategory.name = fields.name.value;
    teamCategory.start_age = fields.start_age.value;
    teamCategory.end_age = fields.end_age.value;
    teamCategory.gender = fields.gender.value;
    teamCategory.active = fields.active.value;
    teamCategory.competition = fields.competition.value;
    teamCategory.remark = fields.remark.value;
  };
  return { ...makeForm(fields), writeForm, readForm };
};

import KwaiForm from '@/components/forms/KwaiForm.vue';
import KwaiField from '@/components/forms/KwaiField.vue';
import KwaiInputText from '@/components/forms/KwaiInputText.vue';
import KwaiTextarea from '@/components/forms/KwaiTextarea.vue';
import KwaiSelect from '@/components/forms/KwaiSelect.vue';
import KwaiCheckbox from '@/components/forms/KwaiCheckbox.vue';

import messages from './lang';

export default {
  components: {
    KwaiForm, KwaiField, KwaiInputText, KwaiTextarea, KwaiSelect, KwaiCheckbox
  },
  i18n: messages,
  data() {
    return {
      genders: [
        {
          value: 0,
          text: this.$t('no_restriction')
        },
        {
          value: 1,
          text: this.$t('male')
        },
        {
          value: 2,
          text: this.$t('female')
        },
      ],
      form: makeTeamTypeForm({
        name: makeField({
          value: '',
          required: true,
          validators: [
            {
              v: notEmpty,
              error: this.$t('form.team_category.name.required'),
            },
          ]
        }),
        start_age: makeField({
          validators: [
            {
              v: isInteger,
              error: this.$t('form.team_category.start_age.numeric'),
            },
          ]
        }),
        end_age: makeField({
          validators: [
            {
              v: isInteger,
              error: this.$t('form.team_category.start_age.numeric'),
            },
          ]
        }),
        gender: makeField({
          value: 0,
        }),
        active: makeField({
          value: true
        }),
        competition: makeField({
          value: true
        }),
        remark: makeField({
          value: ''
        })
      })
    };
  },
  computed: {
    category() {
      return this.$store.state.team.category.active || new TeamCategory();
    },
    error() {
      return this.$store.state.team.category.error;
    },
  },
  beforeRouteEnter(to, from, next) {
    next(async(vm) => {
      if (to.params.id) await vm.fetchData(to.params);
      next();
    });
  },
  async beforeRouteUpdate(to, from, next) {
    await this.fetchData(to.params);
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
      await this.$store.dispatch('team/category/read', {
        id: params.id
      });
      this.form.writeForm(this.category);
    },
    submit() {
      this.form.clearErrors();
      this.form.readForm(this.category);
      this.$store.dispatch('team/category/save', this.category)
        .then((newType) => {
          this.$router.push({
            name: 'team_categories.read',
            params: {
              id: this.category.id
            }
          });
        }).catch(err => {
          console.log(err);
        });
    }
  }
};
</script>
