<template>
  <!-- eslint-disable max-len -->
  <div class="uk-grid">
    <div class="uk-width-5-6">
      <h1>{{ $t('types') }}</h1>
    </div>
    <div class="uk-width-1-6">
      <div class="uk-flex uk-flex-right">
        <div>
          <router-link v-if="canCreate()" class="uk-icon-button uk-link-reset" :to="{ name : 'team_types.create' }">
            <i class="fas fa-plus"></i>
          </router-link>
        </div>
      </div>
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
