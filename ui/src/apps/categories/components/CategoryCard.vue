<template>
  <IconCard :to="linkToCategory" :title="category.name">
    <template v-if="category.icon_picture" slot="icon">
        <inline-svg
          :src="category.icon_picture"
          width="42"
          height="48"
          fill="#c61c18"
        />
    </template>
    <template slot="text">
      {{ category.short_description }}
    </template>
  </IconCard>
</template>

<script>
import InlineSvg from 'vue-inline-svg';
import IconCard from '@/components/IconCard';

/**
 * Component that show a category Card
 */
export default {
  components: {
    InlineSvg,
    IconCard
  },
  props: {
    /**
     * The category to show in the card.
     */
    category: {
      type: Object,
      required: true
    }
  },
  computed: {
    linkToCategory() {
      return {
        name: 'categories.read',
        params: {
          id: this.category.id
        }
      };
    }
  }
};
</script>
