<template>
  <!-- eslint-disable max-len -->
  <div class="hero-container">
    <div
      v-if="picture"
      style="grid-column: 1; max-width: 800px;"
    >
      <img :src="picture" />
    </div>
    <div
      v-if="category"
      :style="grid"
    >
      <div style="display: flex; flex-direction:row; align-items: center; padding-left: 20px;">
        <div
          v-if="category.icon_picture"
          style="margin-right: 20px;"
          >
          <inline-svg
            :src="category.icon_picture"
            width="42"
            height="48"
            fill="white"
          />
        </div>
        <h1>
          {{ category.name }}
        </h1>
      </div>
      <div>
        <p style="padding-left:20px;padding-right: 20px">
          {{ category.description }}
        </p>
      </div>
      <div
        class="kwai-buttons"
        style="display: flex; align-items: flex-end;justify-content: flex-end;"
      >
        <router-link v-if="canCreate"
          class="kwai-icon-button kwai-theme-muted"
          :to="{ name : 'categories.create' }"
        >
          <i class="fas fa-plus"></i>
        </router-link>
        <router-link v-if="$can('update', category)"
          class="kwai-icon-button kwai-theme-muted"
          :to="updateLink"
        >
          <i class="fas fa-edit"></i>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import InlineSvg from 'vue-inline-svg';

import Category from '@/models/Category';

import messages from './lang';

/**
 * Component for header of category page
 */
export default {
  components: {
    InlineSvg
  },
  i18n: messages,
  computed: {
    canCreate() {
      return this.$can('create', Category.type());
    },
    category() {
      return this.$store.getters['category/category'](this.$route.params.id);
    },
    picture() {
      if (this.category) return this.category.header_picture;
      return null;
    },
    grid() {
      if (this.picture !== null) {
        return {
          'grid-column': '2'
        };
      }
      return {
        'grid-column': '1'
      };
    },
    updateLink() {
      return {
        name: 'categories.update',
        params: {
          id: this.category.id
        }
      };
    }
  }
};
</script>
