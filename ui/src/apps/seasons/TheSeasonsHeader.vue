<template>
  <div class="hero-container">
    <div>
      <h1>{{ $t('seasons') }}</h1>
    </div>
    <div style="display:flex; align-items:flex-end;flex-flow:column">
      <router-link
        v-if="canCreate"
        class="kwai-icon-button kwai-theme-muted"
        :to="{ name: 'seasons.create' }"
      >
        <i class="fas fa-plus"></i>
      </router-link>
    </div>
  </div>
</template>

<script>
import Season from '@/models/Season';

import messages from './lang';

export default {
  i18n: messages,
  computed: {
    canCreate() {
      return this.$can('create', Season.type());
    }
  }
};
</script>
