import VueForm, { notEmpty, isInteger } from '@/js/VueForm';

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
            error: this.$t('form.team_type.name.required'),
          },
        ]
      },
      start_age: {
        value: null,
        validators: [
          {
            v: isInteger,
            error: this.$t('form.team_type.start_age.numeric'),
          },
        ]
      },
      end_age: {
        value: null,
        validators: [
          {
            v: isInteger,
            error: this.$t('form.team_type.start_age.numeric'),
          },
        ]
      },
      gender: {
        value: 0,
      },
      active: {
        value: true
      },
      competition: {
        value: true
      },
      remark: {
        value: ''
      }
    };
  },
  methods: {
    writeForm(teamType) {
      this.form.name.value = teamType.name;
      this.form.start_age.value = teamType.start_age;
      this.form.end_age.value = teamType.end_age;
      this.form.gender.value = teamType.gender;
      this.form.active.value = teamType.active;
      this.form.competition.value = teamType.competition;
      this.form.remark.value = teamType.remark;
    },
    readForm(teamType) {
      teamType.name = this.form.name.value;
      teamType.start_age = this.form.start_age.value;
      teamType.end_age = this.form.end_age.value;
      teamType.gender = this.form.gender.value;
      teamType.active = this.form.active.value;
      teamType.competition = this.form.competition.value;
      teamType.remark = this.form.remark.value;
    }
  }
};
