<template>
  <div uk-grid class="uk-light">
    <div class="uk-width-1-1 uk-width-5-6@m">
      <h1 class="uk-margin-remove">{{ $t('news') }}</h1>
      <p>
        {{ $t('all_news') }}
      </p>
    </div>
    <div class="uk-width-1-1 uk-width-1-6@m">
      <div class="uk-flex uk-flex-right">
        <router-link v-if="canCreate"
          class="uk-icon-button uk-link-reset" :to="{ name : 'news.create' }">
          <i class="fas fa-plus"></i>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import Story from '@/models/Story';
import messages from './lang';

export default {
  i18n: messages,
  computed: {
    canCreate() {
      return this.$can('create', Story.type());
    }
  }
};
</script>
