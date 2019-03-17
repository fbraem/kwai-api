<template>
  <!-- eslint-disable max-len -->
  <KwaiForm
    :form="form"
    :save="$t('save')"
    @submit="submit"
  >
    <KwaiField
      name="name"
      :label="$t('form.team_type.name.label')"
    >
      <KwaiInputText :placeholder="$t('form.team_type.name.placeholder')" />
    </KwaiField>
    <KwaiField
      name="start_age"
      :label="$t('form.team_type.start_age.label')"
    >
      <KwaiInputText :placeholder="$t('form.team_type.start_age.placeholder')" />
    </KwaiField>
    <KwaiField
      name="end_age"
      :label="$t('form.team_type.end_age.label')"
    >
      <KwaiInputText :placeholder="$t('form.team_type.end_age.placeholder')" />
    </KwaiField>
    <KwaiField
      name="gender"
      :label="$t('form.team_type.gender.label')"
    >
      <KwaiSelect :items="genders" />
    </KwaiField>
    <KwaiField
      name="active"
      :label="$t('form.team_type.active.label')"
    >
      <KwaiCheckbox />
    </KwaiField>
    <KwaiField
      name="competition"
      :label="$t('form.team_type.competition.label')"
    >
      <KwaiCheckbox />
    </KwaiField>
    <KwaiField
      name="remark"
      :label="$t('form.team_type.remark.label')"
    >
      <KwaiTextarea
        :placeholder="$t('form.team_type.remark.placeholder')"
        :rows="5"
      />
    </KwaiField>
  </KwaiForm>
</template>

<script>
import TeamType from '@/models/TeamType';

import makeForm, { makeField, notEmpty, isInteger } from '@/js/Form';
const makeTeamTypeForm = (fields) => {
  const writeForm = (teamType) => {
    fields.name.value = teamType.name;
    fields.start_age.value = teamType.start_age;
    fields.end_age.value = teamType.end_age;
    fields.gender.value = teamType.gender;
    fields.active.value = teamType.active;
    fields.competition.value = teamType.competition;
    fields.remark.value = teamType.remark;
  };
  const readForm = (teamType) => {
    teamType.name = fields.name.value;
    teamType.start_age = fields.start_age.value;
    teamType.end_age = fields.end_age.value;
    teamType.gender = fields.gender.value;
    teamType.active = fields.active.value;
    teamType.competition = fields.competition.value;
    teamType.remark = fields.remark.value;
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
      teamType: new TeamType(),
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
              error: this.$t('form.team_type.name.required'),
            },
          ]
        }),
        start_age: makeField({
          validators: [
            {
              v: isInteger,
              error: this.$t('form.team_type.start_age.numeric'),
            },
          ]
        }),
        end_age: makeField({
          validators: [
            {
              v: isInteger,
              error: this.$t('form.team_type.start_age.numeric'),
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
    creating() {
      return this.teamType != null && this.teamType.id == null;
    },
    error() {
      return this.$store.state.teamType.error;
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
      this.teamType = await this.$store.dispatch('teamType/read', {
        id: params.id
      });
      this.form.writeForm(this.teamType);
    },
    submit() {
      this.form.clearErrors();
      this.form.readForm(this.teamType);
      this.$store.dispatch('teamType/save', this.teamType)
        .then((newType) => {
          this.$router.push({
            name: 'team_types.read',
            params: {
              id: newType.id
            }
          });
        }).catch(err => {
          console.log(err);
        });
    }
  }
};
</script>
