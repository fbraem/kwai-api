<template>
  <!-- eslint-disable max-len -->
  <section class="uk-section uk-section-default uk-section-small">
    <div class="uk-container">
      <div uk-grid>
        <div class="uk-width-1-1">
          <h4 class="uk-heading-line">
            <span>{{ $t('category') }} &ndash;
              <template v-if="creating">{{ $t('create') }}</template>
              <template v-else>{{ $t('update') }}</template>
            </span>
          </h4>
        </div>
        <div class="uk-width-1-1" uk-grid>
          <div class="uk-width-1-1">
            <form class="uk-form-stacked">
              <field name="name" :label="$t('form.name.label')">
                <uikit-input-text :placeholder="$t('form.name.placeholder')">
                </uikit-input-text>
              </field>
              <field name="short_description" :label="$t('form.short_description.label')">
                <uikit-textarea :placeholder="$t('form.short_description.placeholder')">
                </uikit-textarea>
              </field>
              <field name="description" :label="$t('form.description.label')">
                <uikit-textarea :placeholder="$t('form.description.placeholder')">
                </uikit-textarea>
              </field>
              <field name="remark" :label="$t('form.remark.label')">
                <uikit-textarea :placeholder="$t('form.remark.placeholder')">
                </uikit-textarea>
              </field>
            </form>
          </div>
          <div uk-grid class="uk-width-1-1">
            <div class="uk-width-expand">
            </div>
            <div class="uk-width-auto">
              <button class="uk-button uk-button-primary" :disabled="!$valid" @click="submit">
                <i class="fas fa-save"></i>&nbsp; {{ $t('save') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import Category from '@/models/Category';
import categoryStore from '@/stores/categories';
import registerModule from '@/stores/mixin';

import CategoryForm from './CategoryForm';
import Field from '@/components/forms/Field.vue';
import UikitInputText from '@/components/forms/InputText.vue';
import UikitTextarea from '@/components/forms/Textarea.vue';

import messages from './lang';

export default {
  components: {
    UikitInputText,
    UikitTextarea,
    Field
  },
  i18n: messages,
  mixins: [
    CategoryForm,
    registerModule({ category: categoryStore }),
  ],
  data() {
    return {
      category: new Category()
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
  watch: {
    error(nv) {
      if (nv) {
        if (nv.response.status === 422) {
          this.handleErrors(nv.response.data.errors);
        } else if (nv.response.status === 404){
          // this.error = err.response.statusText;
        } else {
          // TODO: check if we can get here ...
          console.log(nv);
        }
      }
    }
  },
  methods: {
    async fetchData(params) {
      if (params.id) {
        this.category = await this.$store.dispatch('category/read', {
          id: params.id
        });
        this.writeForm(this.category);
      }
    },
    submit() {
      this.clearErrors();
      this.readForm(this.category);
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
