<template>
  <div class="uk-grid">
    <div class="uk-width-5-6">
      <h1>{{ $t('seasons') }}</h1>
    </div>
    <div class="uk-width-1-6">
      <div class="uk-flex uk-flex-right">
        <router-link v-if="canCreate"
          class="uk-icon-button"
          :to="{ name: 'seasons.create' }">
          <i class="fas fa-plus"></i>
        </router-link>
      </div>
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
