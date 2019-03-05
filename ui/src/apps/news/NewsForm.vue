<template>
  <!-- eslint-disable max-len -->
  <div uk-grid>
    <div class="uk-width-1-1">
      <h3 class="uk-heading-line"><span>{{ $t('story') }}</span></h3>
      <KwaiForm :form="form">
        <div uk-grid>
          <div class="uk-width-expand">
            <KwaiField
              name="category"
              :label="$t('form.story.category.label')"
            >
              <KwaiSelect :items="categories" />
            </KwaiField>
          </div>
          <div class="uk-flex uk-flex-bottom">
            <KwaiField name="enabled">
              <KwaiSwitch />
            </KwaiField>
          </div>
        </div>
        <div uk-grid>
          <div class="uk-width-1-2">
            <KwaiField
              name="publish_date"
              :label="$t('form.story.publish_date.label')"
            >
              <KwaiInputText :placeholder="$t('form.story.publish_date.placeholder', { format : dateFormat })" />
            </KwaiField>
          </div>
          <div class="uk-width-1-2">
            <KwaiField
              name="publish_time"
              :label="$t('form.story.publish_time.label')"
            >
              <KwaiInputText :placeholder="$t('form.story.publish_time.placeholder', { format : 'HH:MM' })" />
            </KwaiField>
          </div>
        </div>
        <div uk-grid>
          <div class="uk-width-1-2">
            <KwaiField
              name="end_date"
              :label="$t('form.story.end_date.label')"
            >
              <KwaiInputText :placeholder="$t('form.story.end_date.placeholder', { format : dateFormat })" />
            </KwaiField>
          </div>
          <div class="uk-width-1-2">
            <KwaiField
              name="end_time"
              :label="$t('form.story.end_time.label')"
            >
              <KwaiInputText :placeholder="$t('form.story.end_time.placeholder', { format : 'HH:MM' })" />
            </KwaiField>
          </div>
        </div>
        <KwaiField name="remark" :label="$t('form.story.remark.label')">
          <KwaiTextarea
            :rows="5"
            :placeholder="$t('form.story.remark.placeholder')"
          />
        </KwaiField>
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
                      <KwaiField name="featured" :label="$t('form.story.featured_priority.label')">
                        <KwaiRange />
                      </KwaiField>
                    </div>
                    <div>
                      {{ form.featured.value }}
                    </div>
                  </div>
                </div>
                <div class="uk-width-1-2">
                  <KwaiField
                    name="featured_end_date"
                    :label="$t('form.story.featured_end_date.label')"
                  >
                    <KwaiInputText :placeholder="$t('form.story.featured_end_date.placeholder', { format : dateFormat })" />
                  </KwaiField>
                </div>
                <div class="uk-width-1-2">
                  <KwaiField
                    name="featured_end_time"
                    :label="$t('form.story.featured_end_time.label')"
                  >
                    <KwaiInputText :placeholder="$t('form.story.featured_end_time.placeholder', { format : 'HH:MM' })" />
                  </KwaiField>
                </div>
              </div>
            </div>
          </div>
          <div class="uk-width-1-1">
              <h3 class="uk-heading-line"><span>{{ $t('content') }}</span></h3>
              <KwaiField
                name="title"
                :label="$t('form.content.title.label')"
              >
                <KwaiInputText :placeholder="$t('form.content.title.placeholder')" />
              </KwaiField>
              <KwaiField name="summary" :label="$t('form.content.summary.label')">
                <KwaiTextarea
                  :placeholder="$t('form.content.summary.placeholder')"
                  :rows="5"
                />
              </KwaiField>
              <KwaiField
                name="content"
                :label="$t('form.content.content.label')"
              >
                <KwaiTextarea
                  :placeholder="$t('form.content.content.placeholder')"
                  :rows="15"
                />
              </KwaiField>
          </div>
        </div>
      </KwaiForm>
    </div>
    <div class="uk-width-1-1">
      <div uk-grid>
        <div class="uk-width-expand">
        </div>
        <div class="uk-width-auto">
          <button class="uk-button uk-button-primary"
            :disabled="!form.$valid" @click="submit">
            <i class="fas fa-save"></i>&nbsp; {{ $t('save') }}
          </button>
        </div>
      </div>
    </div>
  </div>
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

import Content from '@/models/Content';
import Category from '@/models/Category';
import Story from '@/models/Story';

import { notEmpty, isDate, isTime } from '@/js/VueForm';

import makeForm from '@/js/Form';
const makeStoryForm = (fields) => {

  const createDatetime = (date, time) => {
    if (time == null || time.length === 0) {
      time = '00:00';
    }
    date += ' ' + time;
    return moment(date, 'L HH:mm', true);
  };

  const publishDatetime = () => {
    return createDatetime(
      fields.publish_date.value,
      fields.publish_time.value
    );
  };
  const endDatetime = () => {
    return createDatetime(
      fields.end_date.value,
      fields.end_time.value
    );
  };
  const featuredEndDatetime = () => {
    return createDatetime(
      fields.featured_end_date.value,
      fields.featured_end_time.value
    );
  };

  const writeForm = (story) => {
    fields.category.value = story.category.id;
    fields.enabled.value = story.enabled;
    if (story.publish_date) {
      fields.publish_date.value = story.localPublishDate;
      fields.publish_time.value = story.localPublishTime;
    }
    if (story.end_date) {
      this.story.end_date.value = story.localEndDate;
      this.story.end_time.value = story.localEndTime;
    }
    fields.featured.value = story.featured;
    if (story.featured_end_date) {
      fields.featured_end_date.value = story.localFeaturedEndDate;
      fields.featured_end_time.value = story.localFeaturedEndTime;
    }
    fields.remark.value = story.remark;

    fields.title.value = story.content.title;
    fields.summary.value = story.content.summary;
    fields.content.value = story.content.content;
  };

  const readForm = (story) => {
    story.timezone = moment.tz.guess();
    story.enabled = fields.enabled.value;
    story.remark = fields.remark.value;
    story.category = new Category();
    story.category.id = fields.category.value;
    story.publish_date = publishDatetime().utc();
    if (fields.end_date.value) {
      story.end_date = endDatetime().utc();
    } else {
      story.end_date = null;
    }
    story.featured = fields.featured.value;
    if (fields.featured_end_date.value) {
      story.featured_end_date = featuredEndDatetime().utc();
    } else {
      story.featured_end_date = null;
    }

    if (!story.contents) {
      story.contents = [ Object.create(null) ];
    }
    story.contents[0].title = fields.title.value;
    story.contents[0].summary = fields.summary.value;
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
import KwaiRange from '@/components/forms/KwaiRange.vue';

export default {
  i18n: messages,
  components: {
    KwaiForm,
    KwaiField,
    KwaiInputText,
    KwaiSwitch,
    KwaiSelect,
    KwaiTextarea,
    KwaiRange
  },
  data() {
    return {
      story: new Story(),
      content: new Content(),
      storyValid: false,
      contentValid: false,
      form: makeStoryForm({
        enabled: {
          value: false
        },
        category: {
          value: 0,
          required: true
        },
        publish_date: {
          value: moment().format('L'),
          required: true,
          validators: [
            {
              v: notEmpty,
              error: this.$t('form.story.publish_date.required')
            },
            {
              v: isDate,
              error: this.$t('form.story.publish_date.invalid', {
                format: moment.localeData().longDateFormat('L')
              })
            },
          ]
        },
        publish_time: {
          value: moment().format('HH:MM'),
          required: true,
          validators: [
            {
              v: notEmpty,
              error: this.$t('form.story.publish_time.required')
            },
            {
              v: isTime,
              error: this.$t('form.story.publish_time.invalid', {
                format: 'HH:MM'
              })
            },
          ]
        },
        end_date: {
          value: null,
          validators: [
            {
              v: isDate,
              error: this.$t('form.story.end_date.invalid', {
                format: moment.localeData().longDateFormat('L')
              })
            },
          ]
        },
        end_time: {
          value: null,
          validators: [
            {
              v: isTime,
              error: this.$t('form.story.end_time.invalid', {
                format: 'HH:MM'
              })
            },
          ]
        },
        featured: {
          value: 0
        },
        featured_end_date: {
          value: null,
          validators: [
            {
              v: isDate,
              error: this.$t('form.story.featured_end_date.invalid', {
                format: moment.localeData().longDateFormat('L')
              })
            },
          ]
        },
        featured_end_time: {
          value: null,
          validators: [
            {
              v: isTime,
              error: this.$t('form.story.featured_end_time.invalid', {
                format: 'HH:MM'
              })
            },
          ]
        },
        remark: {
          value: ''
        },
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
        }
      })
    };
  },
  computed: {
    dateFormat() {
      return '(' + moment.localeData().longDateFormat('L') + ')';
    },
    categories() {
      return this.$store.getters['category/categoriesAsOptions'];
    }
  },
  async created() {
    await this.$store.dispatch('category/browse');
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
        this.story = await this.$store.dispatch('news/read', {
          id: params.id
        });
        this.form.writeForm(this.story);
      }
    },
    async submit() {
      this.form.clearErrors();
      this.form.readForm(this.story);
      this.$store.dispatch('news/save', this.story)
        .then((story) => {
          this.$router.push({
            name: 'news.story',
            params: {
              id: story.id
            }
          });
        })
        .catch(err => {
          console.log(err);
        });
    }
  }
};
</script>
