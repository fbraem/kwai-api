<template>
  <!-- eslint-disable max-len -->
  <div uk-grid>
    <div v-if="picture" class="uk-width-1-1 uk-width-1-2@m uk-width-2-3@l uk-width-3-5@xl uk-flex uk-flex-middle">
        <div>
            <img :src="picture" />
        </div>
    </div>
    <div class="uk-width-1-1" :class="{ 'uk-width-1-2@m' : picture != null, 'uk-width-1-3@l' : picture != null, 'uk-width-2-5@xl' : picture != null }">
      <div v-if="category" uk-grid>
        <div class="uk-width-expand uk-light">
          <div>
            <h1>
              <span v-if="category.icon_picture">
                <img  :src="category.icon_picture" width="40" height="40" uk-svg />&nbsp;
              </span>
              {{ category.name }}</h1>
              <p>
                {{ category.description }}
              </p>
            </div>
          </div>
          <div class="uk-width-1-1 uk-width-1-6@m">
            <div class="uk-flex uk-flex-right">
              <div v-if="$category.isAllowed('create')">
                <router-link  class="uk-icon-button" :to="{ name : 'categories.create' }">
                  <i class="fas fa-plus"></i>
                </router-link>
              </div>
              <div v-if="$category.isAllowed('update', category)">
                <router-link class="uk-icon-button uk-margin-small-left"
                  :to="{ name : 'categories.update', params : { id : category.id } }">
                  <i class="fas fa-edit"></i>
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>

  <style>
  #icon.svg {
    fill:red;
  }
  </style>

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
      return this.$store.getters['category/category'](this.$route.params.id);
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
