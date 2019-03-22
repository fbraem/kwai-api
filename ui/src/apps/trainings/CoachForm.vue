<template>
  <!-- eslint-disable max-len -->
  <div uk-grid>
    <div class="uk-width-1-1">
      <KwaiForm
        :form="form"
        :error="error"
        :save="$t('save')"
        @submit="submit"
      >
        <KwaiField
          v-if="creating"
          name="member"
          :label="$t('training.coaches.form.member.label')"
        >
          <KwaiAutoComplete :placeholder="$t('training.coaches.form.member.placeholder')"
            :items="members"
            :stringResult="(value) => { return value.person.name }">
            <template slot-scope="row">
              {{ row.result.license }} - {{ row.result.person.name }}
            </template>
            <span slot="empty">
              {{ $t('training.coaches.form.member.not_found') }}
            </span>
          </KwaiAutoComplete>
        </KwaiField>
        <div v-else>
          <KwaiField
            name="member"
            :label="$t('training.coaches.form.member.label')"
          >
            <input class="uk-input" type="text" readonly="readonly" v-model="coach.name" />
          </KwaiField>
        </div>
        <div uk-grid>
          <div class="uk-width-expand">
            <KwaiField
              name="diploma"
              :label="$t('training.coaches.form.diploma.label')"
            >
              <KwaiInputText :placeholder="$t('training.coaches.form.diploma.placeholder')" />
            </KwaiField>
          </div>
          <div>
            <KwaiField
              name="active"
              :label="$t('training.coaches.form.active.label')"
            >
              <KwaiSwitch />
            </KwaiField>
          </div>
        </div>
        <KwaiField
          name="description"
          :label="$t('training.coaches.form.description.label')"
        >
          <KwaiTextarea :rows="5" :placeholder="$t('training.coaches.form.description.placeholder')" />
        </KwaiField>
        <KwaiField
          name="remark"
          :label="$t('training.coaches.form.remark.label')"
        >
          <KwaiTextarea :rows="5" :placeholder="$t('training.coaches.form.remark.placeholder')" />
        </KwaiField>
      </KwaiForm>
    </div>
  </div>
</template>

<script>
import TrainingCoach from '@/models/trainings/Coach';
import Member from '@/models/Member';

import makeForm, { makeField } from '@/js/Form';
const makeCoachForm = (fields) => {
  const writeForm = (coach) => {
    fields.description.value = coach.description;
    fields.active.value = coach.active;
    fields.diploma.value = coach.diploma;
    if (coach.member) {
      fields.member.value = coach.member;
    }
    fields.remark.value = coach.remark;
  };

  const readForm = (coach) => {
    coach.description = fields.description.value;
    coach.diploma = fields.diploma.value;
    coach.active = fields.active.value;
    coach.remark = fields.remark.value;
    if (fields.member.value) {
      if (fields.member.value === null) {
        coach.member = null;
      } else {
        coach.member = new Member();
        coach.member.id = fields.member.value.id;
      }
    }
  };

  return { ...makeForm(fields), writeForm, readForm };
};

import KwaiForm from '@/components/forms/KwaiForm';
import KwaiField from '@/components/forms/KwaiField';
import KwaiInputText from '@/components/forms/KwaiInputText';
import KwaiTextarea from '@/components/forms/KwaiTextarea';
import KwaiSwitch from '@/components/forms/KwaiSwitch.vue';
import KwaiAutoComplete from '@/components/forms/KwaiAutoComplete.vue';

import messages from './lang';

export default {
  components: {
    KwaiForm, KwaiField, KwaiInputText, KwaiTextarea,
    KwaiSwitch, KwaiAutoComplete
  },
  i18n: messages,
  data() {
    return {
      coach: new TrainingCoach(),
      items: [],
      form: makeCoachForm({
        member: makeField({
          required: true,
          value: null,
          validators: [
            {
              v: (value) => value !== null,
              error: this.$t('training.coaches.form.member.required')
            },
          ],
          label: this.$t('training.coaches.form.member.label')
        }),
        description: makeField({
          value: '',
          label: this.$t('training.coaches.form.description.label'),
        }),
        diploma: makeField({
          value: null,
          label: this.$t('training.coaches.form.diploma.label')
        }),
        active: makeField({
          value: true,
          label: this.$t('training.coaches.form.active.label')
        }),
        remark: makeField({
          value: '',
          label: this.$t('training.coaches.form.remark.label')
        })
      })
    };
  },
  computed: {
    creating() {
      return this.coach != null && this.coach.id == null;
    },
    error() {
      return this.$store.getters['training/coach/error'];
    },
    members() {
      return this.$store.state.member.members;
    },
  },
  beforeRouteEnter(to, from, next) {
    next(async(vm) => {
      await vm.fetchData(to.params);
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
        } else if (nv.response.status === 404){
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
      if (params.id) {
        this.coach
          = await this.$store.dispatch('training/coach/read', {
            id: params.id
          });
        this.form.writeForm(this.coach);
      } else {
        await this.$store.dispatch('member/browse');
      }
    },
    submit() {
      this.form.clearErrors();
      this.form.readForm(this.coach);
      this.$store.dispatch('training/coach/save', this.coach)
        .then((newCoach) => {
          this.$router.push({
            name: 'trainings.coaches.read',
            params: { id: newCoach.id }
          });
        });
    }
  }
};
</script>
