<template>
  <div class="container mx-auto mt-3">
    <KwaiForm
      :form="form"
      :error="error"
      :save="$t('save')"
      @submit="submit"
    >
      <div style="display: flex;">
        <div style="flex-grow: 1;">
          <KwaiField
            name="category"
            :label="$t('form.page.category.label')"
          >
            <KwaiSelect :items="categories" />
          </KwaiField>
        </div>
        <div style="align-self:flex-end;margin-left: 20px;">
          <KwaiField name="enabled">
            <KwaiSwitch />
          </KwaiField>
        </div>
      </div>
      <div>
        <KwaiField
          name="remark"
          :label="$t('form.page.remark.label')"
        >
          <KwaiTextarea
            :rows="5"
            :placeholder="$t('form.page.remark.placeholder')"
          />
        </KwaiField>
      </div>
      <div>
        <KwaiField
          name="title"
          :label="$t('form.content.title.label')"
        >
          <KwaiInputText :placeholder="$t('form.content.title.placeholder')" />
        </KwaiField>
        <KwaiField
          name="summary"
          :label="$t('form.content.summary.label')"
        >
          <KwaiTextarea
            :placeholder="$t('form.content.summary.placeholder')"
            :rows="5"
          />
        </KwaiField>
        <KwaiField name="content" :label="$t('form.content.content.label')">
          <KwaiTextarea
            :placeholder="$t('form.content.content.placeholder')"
            :rows="15"
          />
        </KwaiField>
      </div>
    </KwaiForm>
  </div>
</template>

<script>
import Category from '@/models/Category';
import Page from '@/models/Page';

import messages from './lang';

import makeForm, { makeField, notEmpty } from '@/js/Form';
const makePageForm = (fields) => {
  const writeForm = (page) => {
    fields.enabled.value = page.enabled;
    fields.remark.value = page.remark;
    fields.category.value = page.category.id;
    fields.priority.value = page.priority;

    fields.title.value = page.content.title;
    fields.summary.value = page.content.summary;
    fields.content.value = page.content.content;
  };
  const readForm = (page) => {
    page.enabled = fields.enabled.value;
    page.remark = fields.remark.value;
    page.category = new Category();
    page.category.id = fields.category.value;
    page.priority = fields.priority.value;

    if (!page.contents) {
      page.contents = [ Object.create(null) ];
    }
    page.contents[0].title = fields.title.value;
    page.contents[0].summary = fields.summary.value;
    page.contents[0].content = fields.content.value;
  };
  return {
    ...makeForm(fields),
    writeForm,
    readForm
  };
};

import KwaiForm from '@/components/forms/KwaiForm.vue';
import KwaiField from '@/components/forms/KwaiField.vue';
import KwaiInputText from '@/components/forms/KwaiInputText.vue';
import KwaiSelect from '@/components/forms/KwaiSelect.vue';
import KwaiTextarea from '@/components/forms/KwaiTextarea.vue';
import KwaiSwitch from '@/components/forms/KwaiSwitch.vue';

export default {
  i18n: messages,
  components: {
    KwaiForm,
    KwaiField,
    KwaiInputText,
    KwaiSwitch,
    KwaiSelect,
    KwaiTextarea,
  },
  data() {
    return {
      page: new Page(),
      form: makePageForm({
        enabled: makeField({
          value: true
        }),
        category: makeField({
          value: null,
          required: true,
          validators: [
            {
              v: notEmpty,
              error: this.$t('form.page.category.required'),
            },
          ]
        }),
        priority: makeField({
          value: 0,
          required: true
        }),
        remark: makeField({
          value: ''
        }),
        title: makeField({
          required: true,
          value: '',
          validators: [
            {
              v: notEmpty,
              error: this.$t('form.content.title.required'),
            },
          ]
        }),
        summary: makeField({
          required: true,
          value: '',
          validators: [
            {
              v: notEmpty,
              error: this.$t('form.content.summary.required'),
            },
          ]
        }),
        content: makeField({
          value: '',
          required: true,
          validators: [
            {
              v: notEmpty,
              error: this.$t('form.content.content.required'),
            },
          ]
        })
      })
    };
  },
  computed: {
    creating() {
      return this.page.id == null;
    },
    error() {
      return this.$store.state.page.error;
    },
    categories() {
      return this.$store.getters['category/categoriesAsOptions'];
    }
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
        this.page = await this.$store.dispatch('page/read', {
          id: params.id
        });
        this.form.writeForm(this.page);
      }
    },
    async submit() {
      this.form.clearErrors();
      this.form.readForm(this.page);
      try {
        this.page = await this.$store.dispatch('page/save', this.page);
        this.$router.push({
          name: 'pages.read',
          params: {
            id: this.page.id
          }
        });
      } catch (error) {
        console.log(error);
      }
    }
  }
};
</script>
