<template>
  <!-- eslint-disable max-len -->
  <div class="hero-container">
    <div v-if="picture">
      <img :src="picture" />
    </div>
    <div
      v-if="category"
      :style="{ 'grid-column': grid }"
    >
      <h1>
        {{ category.name }}
      </h1>
      <h3>
        {{ $t('page') }}
      </h3>
      <p>
        {{ category.description }}
      </p>
      <div
        style="display: flex; justify-content: flex-end; flex-flow: row"
        class="kwai-buttons"
      >
        <router-link
          v-if="canCreate"
          class="kwai-icon-button kwai-theme-muted"
          :to="{ name : 'pages.create' }"
        >
          <i class="fas fa-plus"></i>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import Page from '@/models/Page';

import messages from './lang';

/**
 * Component for category header
 */
export default {
  i18n: messages,
  computed: {
    canCreate() {
      return this.$can('create', Page.type());
    },
    category() {
      /* eslint-disable max-len */
      return this.$store.getters['category/category'](this.$route.params.category);
    },
    picture() {
      if (this.category && this.category.images) {
        return this.category.images.normal;
      }
      return null;
    },
    grid() {
      return this.picture ? '2' : '1 / 3';
    }
  }
};
</script>
