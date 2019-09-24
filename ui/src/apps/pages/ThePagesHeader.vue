<template>
  <div class="hero-container">
    <div>
      <h1>{{ $t('pages') }}</h1>
      <p>
        {{ $t('all_pages') }}
      </p>
    </div>
    <div  style="display:flex; align-items:flex-end;flex-flow:column">
      <router-link
        v-if="canCreate"
        class="kwai-icon-button kwai-theme-muted"
        :to="{ name : 'pages.create' }"
      >
        <i class="fas fa-plus"></i>
      </router-link>
    </div>
  </div>
</template>

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
