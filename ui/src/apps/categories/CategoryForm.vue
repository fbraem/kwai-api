<template>
  <!-- eslint-disable max-len -->
  <div uk-grid>
    <div class="uk-width-1-1">
      <KwaiForm :form="form" :error="error">
        <KwaiField name="name" :label="$t('form.name.label')">
          <KwaiInputText :placeholder="$t('form.name.placeholder')" />
        </KwaiField>
        <KwaiField name="short_description" :label="$t('form.short_description.label')">
          <KwaiTextarea :placeholder="$t('form.short_description.placeholder')" />
        </KwaiField>
        <KwaiField name="description" :label="$t('form.description.label')">
          <KwaiTextarea :placeholder="$t('form.description.placeholder')" />
        </KwaiField>
        <KwaiField name="remark" :label="$t('form.remark.label')">
          <KwaiTextarea :placeholder="$t('form.remark.placeholder')" />
        </KwaiField>
      </KwaiForm>
    </div>
    <div class="uk-width-1-1">
      <div uk-grid>
        <div class="uk-width-expand">
        </div>
        <div class="uk-width-auto">
          <button
            class="uk-button uk-button-primary"
            :disabled="!form.$valid"
            @click="submit"
          >
            <i class="fas fa-save"></i>
            &nbsp; {{ $t('save') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Category from '@/models/Category';

import makeForm from '@/js/Form.js';
import KwaiForm from '@/components/forms/KwaiForm.vue';
import KwaiField from '@/components/forms/Field.vue';
import KwaiInputText from '@/components/forms/KwaiInputText.vue';
import KwaiTextarea from '@/components/forms/Textarea.vue';

const makeCategoryForm = (fields) => {
  const writeForm = (category) => {
    fields.name.value = category.name;
    fields.description.value = category.description;
    fields.remark.value = category.remark;
    fields.short_description.value = category.short_description;
  };
  const readForm = (category) => {
    category.name = fields.name.value;
    category.description = fields.description.value;
    category.remark = fields.remark.value;
    category.short_description = fields.short_description.value;
  };
  return { ...makeForm(fields), writeForm, readForm };
};

import messages from './lang';

import { notEmpty } from '@/js/VueForm';

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
          value: '',
          validators: [
            {
              v: notEmpty,
              error: this.$t('form.short_description.required'),
            },
          ]
        },
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
