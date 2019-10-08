<template>
  <Page>
    <Spinner v-if="$wait.is('pages.browse')" />
    <div
      v-if="pagesMeta"
      class="flex justify-center"
    >
      <Paginator
        :count="pagesMeta.count"
        :limit="pagesMeta.limit"
        :offset="pagesMeta.offset"
        @page="readPage"
      />
    </div>
    <div class="flex flex-wrap justify-center">
      <div
        class="w-full md:w-1/2 lg:w-1/3 p-4"
        v-for="page in pages"
        :key="page.id"
      >
        <PageSummary :page="page" />
      </div>
    </div>
    <div
      v-if="pagesMeta"
      class="flex justify-center"
    >
      <Paginator
        :count="pagesMeta.count"
        :limit="pagesMeta.limit"
        :offset="pagesMeta.offset"
        @page="readPage"
      />
    </div>
    <template slot="sidebar">
      <Sidebar />
    </template>
  </Page>
</template>

<script>
import Page from '@/components/Page';
import PageSummary from './components/PageSummary';
import Paginator from '@/components/Paginator';
import Spinner from '@/components/Spinner';
import Sidebar from './Sidebar';

import messages from './lang';

/**
 * Page for browsing information
 */
export default {
  i18n: messages,
  components: {
    Page,
    Sidebar,
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
