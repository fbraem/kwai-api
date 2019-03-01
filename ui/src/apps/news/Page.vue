<template>
  <!-- eslint-disable max-len -->
  <div uk-grid>
    <div class="uk-width-1-1 uk-width-2-3@m uk-width-4-5@xl">
      <slot></slot>
    </div>
    <div class="uk-width-1-1 uk-width-1-3@m uk-width-1-5@xl">
      <CategoryList
        v-if="categories"
        :categories="categories"
      />
      <h4 class="uk-heading-line uk-text-bold">
        <span>{{ $t('archive') }}</span>
      </h4>
      <template v-for="(year) in archiveYears">
        <div :key="year">
          <h5>{{ year }}</h5>
          <ul class="uk-list">
            <li v-for="(month) in archive[year]" :key="month.month">
              <router-link
                :to="{ name : 'news.archive', params : { year : year, month : month.month }}"
              >
                {{ month.monthName }} {{ year }}
                <span class="uk-badge uk-float-right">
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
