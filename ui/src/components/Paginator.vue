<template>
  <!-- eslint-disable max-len -->
  <ul
    v-if="pageCount > 1"
    class="flex border border-gray-300 rounded w-auto"
  >
    <li v-if="currentPage > 1">
      <a
        class="block hover:text-red-300 hover:bg-red-700 hover:cursor-pointer text-gray-700 border-r border-gray-300 px-3 py-2"
        @click="currentPage -= 1"
      >
        <i class="fas fa-chevron-left"></i>
      </a>
    </li>
    <template v-for="(page, index) in pages">
      <li
        v-if="page === '...'"
        :key="index"
        class="text-gray-700 border-r border-gray-300 px-3 py-2"
      >
        ...
      </li>
      <li
        v-else
        :key="index"
      >
        <a
          :class="{ 'text-gray-700': page !== currentPage, 'text-red-300' : page === currentPage, 'bg-red-700' : page === currentPage }"
          class="block hover:text-red-300 hover:bg-red-700 hover:cursor-pointer border-r border-gray-300 px-3 py-2"
          @click="currentPage = page"
        >
          {{ page }}
        </a>
      </li>
    </template>
    <li
      v-if="currentPage < pageCount"
    >
      <a
        class="block hover:text-red-300 hover:bg-red-700 hover:cursor-pointer text-gray-700 border-r border-gray-300 px-3 py-2"
        @click="currentPage += 1"
      >
        <i class="fas fa-chevron-right"></i>
      </a>
    </li>
  </ul>
</template>

<script>
export default {
  props: {
    maxPagesToShow: {
      type: Number,
      default: 10
    },
    limit: Number,
    offset: Number,
    count: Number
  },
  data() {
    var currentPage = 1;
    for (var offset = 0; offset < this.offset; currentPage++) {
      offset += this.limit;
    }
    var pageCount = Math.ceil(this.count / this.limit);
    return {
      currentPage: currentPage,
      pageCount: pageCount,
      pages: this.pagination(currentPage, pageCount)
    };
  },
  watch: {
    currentPage(nv) {
      if (nv) {
        var offset = 0;
        for (var page = 1; page < nv; page++) {
          offset += this.limit;
        }
        this.pages = this.pagination(nv, this.pageCount);
        this.$emit('page', offset);
      }
    }
  },
  methods: {
    pagination(currentPage, pageCount) {
      const delta = 2;

      let range = [];
      for (let i = Math.max(2, currentPage - delta);
        i <= Math.min(pageCount - 1,
          currentPage + delta); i++) {
        range.push(i);
      }
      if (currentPage - delta > 2) {
        range.unshift('...');
      }
      if (currentPage + delta < pageCount - 1) {
        range.push('...');
      }

      range.unshift(1);
      range.push(pageCount);

      return range;
    }
  }
};
</script>
