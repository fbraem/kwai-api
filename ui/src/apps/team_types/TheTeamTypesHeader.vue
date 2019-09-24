<template>
  <div class="hero-container">
    <div>
      <h1>
        {{ $t('types') }}
      </h1>
    </div>
    <div style="display:flex; align-items:flex-end;flex-flow:column;">
      <router-link
        v-if="canCreate"
        class="kwai-icon-button kwai-theme-muted"
        :to="{ name : 'team_types.create' }"
      >
        <i class="fas fa-plus"></i>
      </router-link>
    </div>
  </div>
</template>

<script>
import TeamType from '@/models/TeamType';

import messages from './lang';

export default {
  i18n: messages,
  computed: {
    canCreate() {
      return this.$can('create', TeamType.type());
    }
  }
};
</script>
