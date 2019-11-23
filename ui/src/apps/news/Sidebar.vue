<template>
  <!-- eslint-disable max-len -->
  <div>
    <CategoryList
      v-if="categories"
      :categories="categories"
    />
    <h3 class="header-line">
      {{ $t('archive') }}
    </h3>
    <template v-for="(year) in archiveYears">
      <div :key="year">
        <h4>{{ year }}</h4>
        <ul>
          <li
            v-for="(month) in archive[year]"
            :key="month.month"
            class="pt-2 last:pb-2"
          >
            <div class="relative">
              <router-link
                :to="{ name : 'news.archive', params : { year : year, month : month.month }}"
                class="cover"
              />
              <span class="text-blue-600">
                {{ month.monthName }} {{ year }}
              </span>
              <span
                class="badge bg-red-700 text-red-300"
                style="float:right"
              >
                {{ month.count }}
              </span>
            </div>
          </li>
        </ul>
      </div>
    </template>
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
      return this.$store.state.category.all;
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
    this.$store.dispatch('news/loadArchive');
  }
};
</script>
