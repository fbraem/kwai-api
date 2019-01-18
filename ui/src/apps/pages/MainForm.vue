<template>
  <!-- eslint-disable max-len -->
  <form class="uk-form-stacked">
    <div uk-grid>
      <div class="uk-width-expand">
        <field name="category" :label="$t('form.page.category.label')">
          <uikit-select :items="categories" />
        </field>
      </div>
      <div class="uk-flex uk-flex-bottom">
        <field name="enabled">
          <uikit-switch />
        </field>
      </div>
    </div>
    <div class="uk-child-width-1-1" uk-grid>
      <field name="remark" :label="$t('form.page.remark.label')">
        <uikit-textarea :rows="5" :placeholder="$t('form.page.remark.placeholder')" />
      </field>
    </div>
  </form>
</template>

<script>
import Page from '@/models/Page';

import messages from './lang';

import MainForm from './MainForm';
import Field from '@/components/forms/Field.vue';
import UikitSwitch from '@/components/forms/Switch.vue';
import UikitSelect from '@/components/forms/Select.vue';
import UikitTextarea from '@/components/forms/Textarea.vue';

export default {
  props: {
    page: {
      type: Page,
      required: true
    }
  },
  i18n: messages,
  mixins: [
    MainForm,
  ],
  components: {
    Field,
    UikitSelect,
    UikitTextarea,
    UikitSwitch
  },
  computed: {
    error() {
      return this.$store.state.page.error;
    },
    categories() {
      return this.$store.getters['category/categoriesAsOptions'];
    },
  },
  async created() {
    this.$emit('formHandler', this.savePage);
    await this.$store.dispatch('category/browse');
  },
  watch: {
    page(nv) {
      this.writeForm(nv);
    },
    $valid(nv) {
      this.$emit('validation', nv);
    },
    error(nv) {
      if (nv) {
        if (nv.response.status === 422) {
          this.handleErrors(nv.response.data.errors);
        } else if (nv.response.status === 404) {
          // this.error = err.response.statusText;
        } else {
          // TODO: check if we can get here ...
          console.log(nv);
        }
      }
    }
  },
  methods: {
    savePage() {
      this.clearErrors();
      this.readForm(this.page);
    }
  }
};
</script>
