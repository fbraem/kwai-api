import VueForm, { notEmpty } from '@/js/VueForm';

import Category from '@/models/Category';

/**
 * Mixin for the Page form
 */
export default {
  mixins: [ VueForm ],
  form() {
    return {
      enabled: {
        value: true
      },
      category: {
        value: null,
        required: true,
        validators: [
          {
            v: notEmpty,
            error: this.$t('form.page.category.required'),
          },
        ]
      },
      priority: {
        value: 0,
        required: true
      },
      remark: {
        value: ''
      }
    };
  },
  methods: {
    writeForm(page) {
      this.form.enabled.value = page.enabled;
      this.form.remark.value = page.remark;
      this.form.category.value = page.category.id;
      this.form.priority.value = page.priority;
    },
    readForm(page) {
      page.enabled = this.form.enabled.value;
      page.remark = this.form.remark.value;
      page.category = new Category();
      page.category.id = this.form.category.value;
      page.priority = this.form.priority.value;
    }
  }
};
