<template>
  <div>
    <PageHeader>
      <h1 class="uk-margin-remove">{{ $t('news') }}</h1>
      <h3 v-if="creating" class="uk-margin-remove">{{ $t('create') }}</h3>
      <h3 v-else class="uk-margin-remove">{{ $t('update') }}</h3>
    </PageHeader>
    <section class="uk-section uk-section-default uk-section-small">
      <div class="uk-container">
        <div uk-grid>
          <div class="uk-width-1-1">
            <h3 class="uk-heading-line"><span>{{ $t('story') }}</span></h3>
            <StoryForm :story="story"
              @validation="storyValidation"
              @formHandler="setStoryFormHandler">
            </StoryForm>
          </div>
        </div>
        <div class="uk-width-1-1">
          <h3 class="uk-heading-line"><span>{{ $t('content') }}</span></h3>
          <ContentForm
            :content="content"
            @validation="contentValidation"
            @formHandler="setContentFormHandler">
          </ContentForm>
        </div>
        <div uk-grid class="uk-width-1-1">
          <div class="uk-width-expand">
          </div>
          <div class="uk-width-auto">
            <button class="uk-button uk-button-primary"
              :disabled="!valid" @click="submit">
              <i class="fas fa-save"></i>&nbsp; {{ $t('save') }}
            </button>
          </div>
        </div>
      </div>
    </section>
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

import newsStore from '@/stores/news';
import categoryStore from '@/stores/categories';
import registerModule from '@/stores/mixin';

import Content from '@/models/Content';
import Story from '@/models/Story';

import PageHeader from '@/site/components/PageHeader.vue';
import StoryForm from './StoryForm.vue';
import ContentForm from './ContentForm.vue';

export default {
  i18n: messages,
  mixins: [
    registerModule(
      {
        news: newsStore
      },
      {
        category: categoryStore
      }
    ),
  ],
  components: {
    PageHeader,
    StoryForm,
    ContentForm,
  },
  data() {
    return {
      story: new Story(),
      content: new Content(),
      storyValid: false,
      contentValid: false
    };
  },
  computed: {
    dateFormat() {
      return '(' + moment.localeData().longDateFormat('L') + ')';
    },
    creating() {
      return this.story != null && this.story.id == null;
    },
    valid() {
      return this.storyValid && this.contentValid;
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
        this.content = this.story.contents[0];
      }
    },
    setStoryFormHandler(fn) {
      this.storyFormHandler = fn;
    },
    setContentFormHandler(fn) {
      this.contentFormHandler = fn;
    },
    storyValidation(valid) {
      this.storyValid = valid;
    },
    contentValidation(valid) {
      this.contentValid = valid;
    },
    async submit() {
      this.storyFormHandler();
      this.contentFormHandler();

      try {
        var newStory = await this.$store.dispatch('news/save', this.story);
        await this.$store.dispatch('news/saveContent', {
          story: newStory,
          content: this.content
        });
        this.$router.push({
          name: 'news.story',
          params: {
            id: newStory.id
          }
        });
      } catch (error) {
        console.log(error);
      }
    }
  }
};
</script>
