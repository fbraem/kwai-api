<template>
  <div class="hero-container">
    <div>
      <h1>
        {{ $t('categories') }}
      </h1>
    </div>
    <div style="display:flex; align-items:flex-end;flex-flow:column;">
      <router-link
        v-if="canCreate"
        class="secondary:kwai-icon-button"
        :to="{ name : 'categories.create' }"
      >
        <i class="fas fa-plus"></i>
      </router-link>
    </div>
  </div>
</template>

<script>
import Category from '@/models/Category';
import messages from './lang';

/**
 * Component for header of the browse page
 */
export default {
  i18n: messages,
  computed: {
    canCreate() {
      return this.$can('create', Category.type());
    }
  }
};
</script>
