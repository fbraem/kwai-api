<template>
  <!-- eslint-disable max-len -->
  <form class="uk-form-stacked">
    <div uk-grid>
      <div class="uk-width-expand">
        <field name="category" :label="$t('form.story.category.label')">
          <uikit-select :items="categories" />
        </field>
      </div>
      <div class="uk-flex uk-flex-bottom">
        <field name="enabled">
          <uikit-switch />
        </field>
      </div>
    </div>
    <div uk-grid>
      <div class="uk-width-1-2">
        <field name="publish_date" :label="$t('form.story.publish_date.label')">
          <uikit-input-text :placeholder="$t('form.story.publish_date.placeholder', { format : dateFormat })" />
        </field>
      </div>
      <div class="uk-width-1-2">
        <field name="publish_time" :label="$t('form.story.publish_time.label')">
          <uikit-input-text :placeholder="$t('form.story.publish_time.placeholder', { format : 'HH:MM' })" />
        </field>
      </div>
    </div>
    <div uk-grid>
      <div class="uk-width-1-2">
        <field name="end_date" :label="$t('form.story.end_date.label')">
          <uikit-input-text :placeholder="$t('form.story.end_date.placeholder', { format : dateFormat })" />
        </field>
      </div>
      <div class="uk-width-1-2">
        <field name="end_time" :label="$t('form.story.end_time.label')">
          <uikit-input-text :placeholder="$t('form.story.end_time.placeholder', { format : 'HH:MM' })" />
        </field>
      </div>
    </div>
    <field name="remark" :label="$t('form.story.remark.label')">
      <uikit-textarea :rows="5" :placeholder="$t('form.story.remark.placeholder')" />
    </field>
    <div uk-grid>
      <div class="uk-width-1-1">
        <div class="uk-tile uk-tile-default uk-tile-muted uk-padding-small">
          <div uk-grid>
            <div class="uk-width-1-1">
              <h3>{{ $t('featured') }}</h3>
              <blockquote class="uk-text-meta">
                {{ $t('featured_hint') }}
              </blockquote>
            </div>
            <div class="uk-width-1-1">
              <div uk-grid>
                <div class="uk-width-expand">
                  <field name="featured" :label="$t('form.story.featured_priority.label')">
                    <uikit-range />
                  </field>
                </div>
                <div>
                  {{ form.featured.value }}
                </div>
              </div>
            </div>
            <div class="uk-width-1-2">
              <field name="featured_end_date" :label="$t('form.story.featured_end_date.label')">
                <uikit-input-text :placeholder="$t('form.story.featured_end_date.placeholder', { format : dateFormat })" />
              </field>
            </div>
            <div class="uk-width-1-2">
              <field name="featured_end_time" :label="$t('form.story.featured_end_time.label')">
                <uikit-input-text :placeholder="$t('form.story.featured_end_time.placeholder', { format : 'HH:MM' })" />
              </field>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>

<style scoped>
    input[type=file] {
        position: absolute;
        left: -99999px;
    }
</style>

<script>
import moment from 'moment';
import 'moment-timezone';

import messages from './lang';

import StoryForm from './StoryForm';

import Field from '@/components/forms/Field.vue';
import UikitInputText from '@/components/forms/InputText.vue';
import UikitSelect from '@/components/forms/Select.vue';
import UikitTextarea from '@/components/forms/Textarea.vue';
import UikitSwitch from '@/components/forms/Switch.vue';
import UikitRange from '@/components/forms/Range.vue';

export default {
  i18n: messages,
  props: [
    'story',
  ],
  mixins: [
    StoryForm,
  ],
  components: {
    Field,
    UikitInputText,
    UikitSelect,
    UikitTextarea,
    UikitSwitch,
    UikitRange
  },
  computed: {
    dateFormat() {
      return '(' + moment.localeData().longDateFormat('L') + ')';
    },
    creating() {
      return this.story != null && this.story.id == null;
    },
    error() {
      return this.$store.state.news.error;
    },
    categories() {
      return this.$store.getters['category/categoriesAsOptions'];
    },
  },
  async created() {
    this.$emit('formHandler', this.saveStory);
    await this.$store.dispatch('category/browse');
  },
  watch: {
    story(nv) {
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
    saveStory() {
      this.clearErrors();
      this.readForm(this.story);
    }
  }
};
</script>
