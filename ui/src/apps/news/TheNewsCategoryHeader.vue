<template>
  <div class="hero-container">
    <div v-if="picture">
      <div>
        <img :src="picture" />
      </div>
    </div>
    <div v-if="category">
      <h1>
        {{ $t('news') }}
      </h1>
      <h3>
        {{ category.name }}
      </h3>
      <p>
        {{ category.description }}
      </p>
    </div>
    <div style="display:flex; align-items:flex-end;flex-flow:column">
      <router-link
        v-if="canCreate"
        class="secondary:kwai-icon-button"
        :to="{ name : 'news.create' }"
      >
        <i class="fas fa-plus"></i>
      </router-link>
    </div>
  </div>
</template>

<script>
import Story from '@/models/Story';
import messages from './lang';

/**
 * Component for header of category page
 */
export default {
  i18n: messages,
  computed: {
    canCreate() {
      return this.$can('create', Story.type());
    },
    category() {
      /* eslint-disable max-len */
      if (this.$route.params.category) {
        return this.$store.getters['category/category'](this.$route.params.category);
      }
      return null;
      /* eslint-enable max-len */
    },
    picture() {
      if (this.category && this.category.images) {
        return this.category.images.normal;
      }
      return null;
    },
  }
};
</script>
