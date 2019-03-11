<template>
  <!-- eslint-disable max-len -->
  <div uk-grid>
    <div
      v-if="picture"
      class="uk-width-1-1 uk-width-1-2@m uk-width-2-3@l uk-width-3-5@xl uk-flex uk-flex-middle"
    >
      <div>
        <img :src="picture" />
      </div>
    </div>
    <div
      class="uk-width-1-1"
      :class="{ 'uk-width-1-2@m' : picture != null, 'uk-width-1-3@l' : picture != null, 'uk-width-2-5@xl' : picture != null }"
    >
      <div
        class="uk-light"
        uk-grid
      >
        <div class="uk-width-1-1 uk-width-5-6@m">
          <div v-if="category">
            <h1 class="uk-margin-remove">
              {{ category.name }}
            </h1>
            <h3 class="uk-margin-remove">
              {{ $t('page') }}
            </h3>
            <p>
              {{ category.description }}
            </p>
          </div>
        </div>
        <div class="uk-width-1-1 uk-width-1-6@m">
          <div class="uk-flex uk-flex-right">
            <router-link
              v-if="canCreate"
              class="uk-icon-button uk-link-reset"
              :to="{ name : 'pages.create' }"
            >
              <i class="fas fa-plus"></i>
            </router-link>
          </div>
        </div>
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
      return this.$store.getters['category/category'](this.$route.params.category);
    },
    picture() {
      if (this.category && this.category.images) {
        return this.category.images.normal;
      }
      return null;
    }
  }
};
</script>
