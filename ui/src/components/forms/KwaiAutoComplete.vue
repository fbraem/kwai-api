<template>
  <!-- eslint-disable max-len -->
  <div class="autocomplete">
    <input
      v-model="search"
      :id="id"
      :name="id"
      class="appearance-once block w-full bg-gray-200 border rounded py-3 px-4 mb-3 leading-thight focus:outline-none focus:shadow-outline"
      :class="{ 'text-red-600' : field.errors.length > 0 }"
      type="search"
      :required="field.required"
      v-bind="$attrs"
      @input="onChange"
      @keydown.down="onArrowDown"
      @keydown.up="onArrowUp"
      @keydown.enter="onEnter"
      @keydown.esc.prevent="onEscape"
    />
    <ul
      class="autocomplete-results"
      v-show="isOpen"
    >
      <li v-if="isLoading">
        <div class="text-center">
          <i class="fas fa-spinner fa-2x fa-spin"></i>
        </div>
      </li>
      <li v-if="!isLoading && results != null && results.length == 0">
        <slot name="empty">
          Nothing found ...
        </slot>
      </li>
      <template v-if="items != null && items.length > 0 && !isLoading">
        <li
          class="autocomplete-result"
          v-for="(result, i) in results" :key="i"
          @click="setResult(result)"
          :class="{ 'is-active': i === arrowCounter }">
          <slot :result="result" />
        </li>
      </template>
    </ul>
  </div>
</template>

<style>
.autocomplete-results {
  @apply p-0 m-0 overflow-auto;
  border: 1px solid #eeeeee;
  height: 120px;
}

.autocomplete-result {
  @apply text-left cursor-pointer;
  padding: 4px 2px;
}

.autocomplete-result.is-active,
.autocomplete-result:hover {
  background-color: #4AAE9B;
  color: white;
}
</style>

<script>
export default {
  inject: {
    field: 'field',
    id: 'id'
  },
  props: {
    items: {
      type: Array,
      required: false,
      default: () => [],
    },
    async: {
      type: Boolean,
      required: false,
      default: false
    },
    stringResult: {
      type: Function,
      required: false,
      default: (value) => value
    },
  },
  data() {
    return {
      search: '',
      results: [],
      isOpen: false,
      isLoading: false,
      arrowCounter: -1
    };
  },
  mounted() {
    document.addEventListener('click', this.handleClickOutside);
  },
  destroyed() {
    document.removeEventListener('click', this.handleClickOutside);
  },
  watch: {
    items(nv, ov) {
      if (this.async) {
        this.results = nv;
        this.isOpen = true;
        this.isLoading = false;
      }
    }
  },
  methods: {
    onChange() {
      if (this.search.length === 0) {
        this.results = [];
        this.isOpen = false;
        this.arrowCounter = -1;
        this.field.value = null;
        return;
      }

      this.isOpen = true;
      if (this.async) {
        this.isLoading = true;
      } else {
        this.filterResults();
      }
      this.$emit('input', this.search);
    },
    onArrowDown() {
      if (this.arrowCounter < this.results.length) {
        this.arrowCounter = this.arrowCounter + 1;
      }
    },
    onArrowUp() {
      if (this.arrowCounter > 0) {
        this.arrowCounter = this.arrowCounter - 1;
      }
    },
    onEnter() {
      this.setResult(this.results[this.arrowCounter]);
      this.arrowCounter = -1;
    },
    onEscape() {
      if (this.field.value) {
        this.setResult(this.field.value);
      } else {
        this.search = '';
        this.isOpen = false;
      }
    },
    handleClickOutside(evt) {
      if (!this.$el.contains(evt.target)) {
        this.isOpen = false;
        this.arrowCounter = -1;
      }
      if (this.field.value) {
        this.setResult(this.field.value);
      }
    },
    filterResults() {
      this.results = this.items.filter(
        (item) => {
          var s = this.stringResult(item);
          return s.toLowerCase().indexOf(this.search.toLowerCase()) > -1;
        }
      );
    },
    setResult(result) {
      this.search = this.stringResult(result);
      this.results = [];
      this.isOpen = false;
      this.field.value = result;
    }
  }
};
</script>
