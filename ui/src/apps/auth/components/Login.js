import VueForm, { notEmpty, isEmail } from '@/js/VueForm';

/**
 * Mixin for the Login form.
 */
export default {
  mixins: [ VueForm ],
  form() {
    return {
      email: {
        required: true,
        value: '',
        validators: [
          {
            v: notEmpty,
            error: this.$t('email.required')
          },
          {
            v: isEmail,
            error: this.$t('email.invalid')
          },
        ]
      },
      password: {
        required: true,
        value: '',
        validators: [
          {
            v: notEmpty,
            error: this.$t('password.required')
          },
        ]
      }
    };
  },
  methods: {
    writeForm(user) {
      this.form.email.value = user.email;
      this.form.password.value = user.password;
    },
    readForm(user) {
      user.email = this.form.email.value;
      user.password = this.form.password.value;
    }
  }
};
