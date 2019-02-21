<template>
  <div class="uk-child-width-1-1" uk-grid>
    <div>
      <h3 class="uk-heading-line"><span>{{ $t('page') }}</span></h3>
      <MainForm :page="page"
        @validation="pageValidation"
        @formHandler="setPageFormHandler">
      </MainForm>
    </div>
    <div>
      <h3 class="uk-heading-line"><span>{{ $t('content') }}</span></h3>
      <ContentForm
        :content="content"
        @validation="contentValidation"
        @formHandler="setContentFormHandler">
      </ContentForm>
    </div>
    <div uk-grid>
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
</template>

<script>
import Content from '@/models/Content';
import Page from '@/models/Page';

import messages from './lang';

import MainForm from './MainForm.vue';
import ContentForm from './ContentForm.vue';

export default {
  i18n: messages,
  components: {
    MainForm,
    ContentForm,
  },
  data() {
    return {
      page: new Page(),
      content: new Content(),
      pageValid: false,
      contentValid: false
    };
  },
  computed: {
    creating() {
      return this.page.id == null;
    },
    error() {
      return this.$store.state.page.error;
    },
    valid() {
      return this.pageValid && this.contentValid;
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
        this.page = await this.$store.dispatch('page/read', {
          id: params.id
        });
        this.content = this.page.contents[0];
      }
    },
    setPageFormHandler(fn) {
      this.pageFormHandler = fn;
    },
    setContentFormHandler(fn) {
      this.contentFormHandler = fn;
    },
    pageValidation(valid) {
      this.pageValid = valid;
    },
    contentValidation(valid) {
      this.contentValid = valid;
    },
    async submit() {
      this.pageFormHandler();
      try {
        this.page = await this.$store.dispatch('page/save', this.page);
        this.contentFormHandler();
        await this.$store.dispatch('page/saveContent', {
          page: this.page,
          content: this.content
        });
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
