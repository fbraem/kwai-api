<template>
  <KwaiForm
    :form="form"
    :error="error"
    :save="$t('save')"
    @submit="submit"
  >
    <KwaiField
      name="name"
      :label="$t('form.name.label')"
    >
      <KwaiInputText :placeholder="$t('form.name.placeholder')" />
    </KwaiField>
    <KwaiField
      name="slug"
      :label="$t('form.slug.label')"
    >
      <KwaiInputText :placeholder="$t('form.slug.placeholder')" />
    </KwaiField>
    <KwaiField
      name="short_description"
      :label="$t('form.short_description.label')"
    >
      <KwaiTextarea :placeholder="$t('form.short_description.placeholder')" />
    </KwaiField>
    <KwaiField
      name="description"
      :label="$t('form.description.label')"
    >
      <KwaiTextarea :placeholder="$t('form.description.placeholder')" />
    </KwaiField>
    <KwaiField
      name="remark"
      :label="$t('form.remark.label')"
    >
      <KwaiTextarea :placeholder="$t('form.remark.placeholder')" />
    </KwaiField>
  </KwaiForm>
</template>

<script>
import Category from '@/models/Category';

import makeForm, { makeField, notEmpty } from '@/js/Form.js';
import KwaiForm from '@/components/forms/KwaiForm.vue';
import KwaiField from '@/components/forms/KwaiField.vue';
import KwaiInputText from '@/components/forms/KwaiInputText.vue';
import KwaiTextarea from '@/components/forms/KwaiTextarea.vue';

const makeCategoryForm = (fields) => {
  const writeForm = (category) => {
    fields.name.value = category.name;
    fields.slug.value = category.slug;
    fields.description.value = category.description;
    fields.remark.value = category.remark;
    fields.short_description.value = category.short_description;
  };
  const readForm = (category) => {
    category.name = fields.name.value;
    category.slug = fields.slug.value;
    category.description = fields.description.value;
    category.remark = fields.remark.value;
    category.short_description = fields.short_description.value;
  };
  return { ...makeForm(fields), writeForm, readForm };
};

import messages from './lang';

/**
 * Component for creating or updating a category
 */
export default {
  components: {
    KwaiForm,
    KwaiInputText,
    KwaiTextarea,
    KwaiField
  },
  i18n: messages,
  data() {
    return {
      category: new Category(),
      form: makeCategoryForm({
        name: makeField({
          required: true,
          validators: [
            {
              v: notEmpty,
              error: this.$t('form.name.required'),
            },
          ]
        }),
        slug: makeField({
          required: true,
          validators: [
            {
              v: notEmpty,
              error: this.$t('form.slug.required'),
            },
          ]
        }),
        description: makeField(),
        remark: makeField(),
        short_description: makeField({
          required: true,
          validators: [
            {
              v: notEmpty,
              error: this.$t('form.short_description.required'),
            },
          ]
        }),
      })
    };
  },
  computed: {
    creating() {
      return this.category != null && this.category.id == null;
    },
    error() {
      return this.$store.state.category.error;
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
  methods: {
    async fetchData(params) {
      if (params.id) {
        this.category = await this.$store.dispatch('category/read', {
          id: params.id
        });
        this.form.writeForm(this.category);
      }
    },
    submit() {
      this.form.clearErrors();
      this.form.readForm(this.category);
      this.$store.dispatch('category/save', this.category)
        .then((newCategory) => {
          this.$router.push({
            name: 'categories.read',
            params: { id: newCategory.id }
          });
        })
        .catch(err => {
          console.log(err);
        });
    }
  }
};
</script>
