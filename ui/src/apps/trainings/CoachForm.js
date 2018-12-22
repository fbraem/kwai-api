import VueForm from '@/js/VueForm';

import Member from '@/models/Member';

export default {
  mixins: [ VueForm ],
  form() {
    return {
      member: {
        required: true,
        value: 0,
        label: this.$t('training.coaches.form.member.label')
      },
      description: {
        value: '',
        label: this.$t('training.coaches.form.description.label'),
      },
      diploma: {
        value: null,
        label: this.$t('training.coaches.form.diploma.label')
      },
      active: {
        value: true,
        label: this.$t('training.coaches.form.active.label')
      },
      remark: {
        value: '',
        label: this.$t('training.coaches.form.remark.label')
      }
    };
  },
  methods: {
    writeForm(coach) {
      this.form.description.value = coach.description;
      this.form.active.value = coach.active;
      this.form.diploma.value = coach.diploma;
      if (coach.member) {
        this.form.member.value = coach.member;
      }
      this.form.remark.value = coach.remark;
    },
    readForm(coach) {
      coach.description = this.form.description.value;
      coach.diploma = this.form.diploma.value;
      coach.active = this.form.active.value;
      coach.remark = this.form.remark.value;
      if (this.form.member.value) {
        if (this.form.member.value === null) {
          coach.member = null;
        } else {
          coach.member = new Member();
          coach.member.id = this.form.member.value.id;
        }
      }
      return coach;
    }
  }
};
