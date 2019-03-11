<template>
  <!-- eslint-disable max-len -->
  <div>
    <Page>
      <Spinner v-if="$wait.is('pages.browse')" />
      <div
        v-else
        class="uk-child-width-1-1"
        uk-grid
      >
        <div v-if="pagesMeta">
          <Paginator
            :count="pagesMeta.count"
            :limit="pagesMeta.limit"
            :offset="pagesMeta.offset"
            @page="readPage"
          />
        </div>
        <div
          class="uk-grid-medium uk-child-width-1-2@s uk-child-width-1-3@m uk-child-width-1-4@l uk-grid-match"
          uk-grid
          >
          <PageSummary
            v-for="page in pages"
            :page="page"
            :key="page.id"
          />
        </div>
        <div v-if="pagesMeta">
          <Paginator
            :count="pagesMeta.count"
            :limit="pagesMeta.limit"
            :offset="pagesMeta.offset"
            @page="readPage"
          />
        </div>
      </div>
    </Page>
  </div>
</template>

<script>
import Page from './Page.vue';
import PageSummary from './components/PageSummary.vue';
import Paginator from '@/components/Paginator.vue';
import Spinner from '@/components/Spinner.vue';

import messages from './lang';

/**
 * Page for browsing information
 */
export default {
  i18n: messages,
  components: {
    Page,
    PageSummary,
    Paginator,
    Spinner
  },
  computed: {
    pages() {
      return this.$store.state.page.pages;
    },
    pageCount() {
      if (this.pages) return this.pages.length;
      return -1;
    },
    pagesMeta() {
      return null;
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
    fetchData(params) {
      this.$store.dispatch('page/browse', {
        category: params.category
      });
    },
    readPage(offset) {
      console.log(offset);
    }
  }
};
</script>
