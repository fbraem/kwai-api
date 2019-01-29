import VueForm from '@/js/VueForm';

export default {
  mixins: [ VueForm ],
  form() {
    return {
      start_age: {
        value: null
      },
      end_age: {
        value: null
      },
      gender: {
        value: 0
      }
    };
  },
  methods: {
    writeForm(filter) {
      this.form.start_age.value = filter.start_age;
      this.form.end_age.value = filter.end_age;
      this.form.gender.value = filter.gender;
    },
    readForm(filter) {
      filter.start_age = this.form.start_age.value;
      filter.end_age = this.form.end_age.value;
      filter.gender = this.form.gender.value;
    }
  }
};
