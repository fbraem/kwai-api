<template>
  <!-- eslint-disable max-len -->
  <Page>
    <slot></slot>
    <template slot="sidebar">
      <Sidebar />
    </template>
  </Page>
</template>

<script>
import messages from './lang';

import Page from '@/components/Page';
import Sidebar from './Sidebar';

export default {
  i18n: messages,
  components: {
    Page,
    Sidebar
  },
  computed: {
    categories() {
      return this.$store.state.category.categories;
    },
    archiveYears() {
      var archive = this.$store.state.news.archive;
      var sorted = Object.keys(archive).reverse();
      return sorted;
    },
    archive() {
      return this.$store.state.news.archive;
    }
  },
  created() {
    this.$store.dispatch('category/browse');
    this.$store.dispatch('news/loadArchive');
  }
};
</script>
