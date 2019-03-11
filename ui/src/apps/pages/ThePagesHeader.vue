<template>
  <div uk-grid class="uk-light">
    <div class="uk-width-1-1 uk-width-5-6@m">
      <h1 class="uk-margin-remove">{{ $t('pages') }}</h1>
      <p>
        {{ $t('all_pages') }}
      </p>
    </div>
    <div class="uk-width-1-1 uk-width-1-6@m">
      <div class="uk-flex uk-flex-right">
        <router-link v-if="canCreate"
          class="uk-icon-button uk-link-reset" :to="{ name : 'pages.create' }">
          <i class="fas fa-plus"></i>
        </router-link>
      </div>
    </div>
  </div></template>

<script>
import Page from '@/models/Page';
import messages from './lang';

/**
 * Component for header of pages
 */
export default {
  i18n: messages,
  computed: {
    canCreate() {
      return this.$can('create', Page.type());
    }
  }
};
</script>
