import VueForm, { notEmpty } from '@/js/VueForm';

/**
 * Mixin for the Category form.
 */
export default {
  mixins: [ VueForm ],
  form() {
    return {
      name: {
        required: true,
        value: '',
        validators: [
          {
            v: notEmpty,
            error: this.$t('form.name.required'),
          },
        ]
      },
      description: {
        value: '',
      },
      remark: {
        value: ''
      },
      short_description: {
        required: true,
        value: ''
      },
      validators: [
        {
          v: notEmpty,
          error: this.$t('form.short_description.required'),
        },
      ]
    };
  },
  methods: {
    writeForm(category) {
      this.form.name.value = category.name;
      this.form.description.value = category.description;
      this.form.remark.value = category.remark;
      this.form.short_description.value = category.short_description;
    },
    readForm(category) {
      category.name = this.form.name.value;
      category.description = this.form.description.value;
      category.remark = this.form.remark.value;
      category.short_description = this.form.short_description.value;
    }
  }
};
