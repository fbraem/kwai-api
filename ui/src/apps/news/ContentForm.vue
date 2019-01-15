<template>
  <form class="uk-form-stacked">
    <field name="title" :label="$t('form.content.title.label')">
      <uikit-input-text :placeholder="$t('form.content.title.placeholder')" />
    </field>
    <field name="summary" :label="$t('form.content.summary.label')">
      <uikit-textarea
        :placeholder="$t('form.content.summary.placeholder')"
        :rows="5" />
    </field>
    <field name="content" :label="$t('form.content.content.label')">
      <uikit-textarea
        :placeholder="$t('form.content.content.placeholder')"
        :rows="15" />
    </field>
  </form>
</template>

<style scoped>
    input[type=file] {
        position: absolute;
        left: -99999px;
    }
</style>

<script>
import messages from './lang';

import ContentForm from './ContentForm';

import Field from '@/components/forms/Field.vue';
import UikitInputText from '@/components/forms/InputText.vue';
import UikitTextarea from '@/components/forms/Textarea.vue';

export default {
  i18n: messages,
  props: [
    'content',
  ],
  mixins: [
    ContentForm,
  ],
  components: {
    Field,
    UikitInputText,
    UikitTextarea,
  },
  computed: {
    error() {
      return this.$store.state.news.error;
    },
  },
  created() {
    this.$emit('formHandler', this.saveContent);
  },
  watch: {
    content(nv) {
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
    saveContent() {
      this.clearErrors();
      this.readForm(this.content);
    }
  }
};
</script>
