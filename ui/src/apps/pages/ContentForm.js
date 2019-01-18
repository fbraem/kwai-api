import VueForm, { notEmpty } from '@/js/VueForm';

/**
 * Mixin for the Content form.
 */
export default {
  mixins: [ VueForm ],
  form() {
    return {
      title: {
        required: true,
        value: '',
        validators: [
          {
            v: notEmpty,
            error: this.$t('form.content.title.required'),
          },
        ]
      },
      summary: {
        required: true,
        value: '',
        validators: [
          {
            v: notEmpty,
            error: this.$t('form.content.summary.required'),
          },
        ]
      },
      content: {
        value: '',
        required: true,
        validators: [
          {
            v: notEmpty,
            error: this.$t('form.content.content.required'),
          },
        ]
      },
    };
  },
  methods: {
    writeForm(content) {
      this.form.title.value = content.title;
      this.form.summary.value = content.summary;
      this.form.content.value = content.content;
    },
    readForm(content) {
      content.title = this.form.title.value;
      content.summary = this.form.summary.value;
      content.content = this.form.content.value;
    }
  }
};
