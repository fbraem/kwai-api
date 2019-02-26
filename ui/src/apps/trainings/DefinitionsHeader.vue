<template>
  <!-- eslint-disable max-len -->
  <div class="uk-grid">
    <div class="uk-width-5-6">
      <h1>{{ $t('training.definitions.title') }}</h1>
    </div>
    <div class="uk-width-1-6">
      <div class="uk-flex uk-flex-right">
        <router-link v-if="canCreate" class="uk-icon-button uk-link-reset" :to="{ name : 'trainings.definitions.create' }">
          <i class="fas fa-plus"></i>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import Definition from '@/models/trainings/Definition';

import messages from './lang';

export default {
  i18n: messages,
  canCreate() {
    return this.$can('create', Definition.type());
  }
};
</script>
