<template>
  <div uk-grid class="uk-light">
    <div v-if="picture" class="uk-width-1-1 uk-width-1-2@m uk-width-2-3@l uk-width-3-5@xl uk-flex uk-flex-middle">
        <div>
            <img :src="picture" />
        </div>
    </div>
    <div v-if="category" class="uk-width-1-1 uk-width-5-6@m">
      <h1 class="uk-margin-remove">{{ $t('news') }}</h1>
      <h3 class="uk-margin-remove">{{ category.name }}</h3>
      <p>
        {{ category.description }}
      </p>
    </div>
    <div class="uk-width-1-1 uk-width-1-6@m">
      <div class="uk-flex uk-flex-right">
        <router-link v-if="$story.isAllowed('create')"
          class="uk-icon-button uk-link-reset"
          :to="{ name : 'news.create' }">
          <i class="fas fa-plus"></i>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import messages from './lang';

import categoryStore from '@/stores/categories';
import registerModule from '@/stores/mixin';

export default {
  i18n: messages,
  mixins: [
    registerModule(
      {
        category: categoryStore
      }
    ),
  ],
  computed: {
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
