<template>
  <div class="hero-container">
    <div>
      <h1>{{ $t('news') }}</h1>
      <p>
        {{ $t('all_news') }}
      </p>
    </div>
    <div style="display:flex; align-items:flex-end;flex-flow:column">
      <router-link v-if="canCreate"
        class="kwai-icon-button kwai-dark" :to="{ name : 'news.create' }">
        <i class="fas fa-plus"></i>
      </router-link>
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
