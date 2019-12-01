<template>
  <div class="bg-gray-300">
    <div class="container mx-auto flex flex-row flex-wrap justify-center p-4">
      <div
        v-for="category in categories"
        :key="category.id"
        class="w-full sm:w-1/2 md:w-1/3"
      >
        <CategoryCard :category="category" />
      </div>
    </div>
  </div>
</template>

<script>
import CategoryCard from './CategoryCard';

export default {
  components: {
    CategoryCard
  },
  props: {
    categories: {
      type: Array,
      required: true
    }
  }
};
</script>
