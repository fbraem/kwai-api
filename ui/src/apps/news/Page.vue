<template>
  <!-- eslint-disable max-len -->
  <div class="page-container">
    <div style="grid-area: page-content">
      <slot></slot>
    </div>
    <div style="grid-area: page-sidebar">
      <CategoryList
        v-if="categories"
        :categories="categories"
      />
      <h4>
        <span>{{ $t('archive') }}</span>
      </h4>
      <template v-for="(year) in archiveYears">
        <div :key="year">
          <h5>{{ year }}</h5>
          <ul class="kwai-list">
            <li v-for="(month) in archive[year]" :key="month.month">
              <router-link
                :to="{ name : 'news.archive', params : { year : year, month : month.month }}"
              >
                {{ month.monthName }} {{ year }}
                <span
                  class="kwai-badge kwai-badge-primary kwai-badge-rounded"
                  style="float:right"
                >
                  {{ month.count }}
                </span>
              </router-link>
            </li>
          </ul>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
import messages from './lang';

import CategoryList from '@/apps/categories/components/CategoryList.vue';

export default {
  i18n: messages,
  components: {
    CategoryList
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
