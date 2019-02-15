<template>
  <!-- eslint-disable max-len -->
  <div uk-grid>
    <div class="uk-width-expand">
      <h1>{{ $t('categories') }}</h1>
    </div>
    <div class="uk-width-1-1 uk-width-1-6@m">
      <div class="uk-flex uk-flex-right">
        <div v-if="$category.isAllowed('create')">
          <router-link class="uk-icon-button" :to="{ name : 'categories.create' }">
            <i class="fas fa-plus"></i>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import messages from './lang';

export default {
  i18n: messages
};
</script>
