<template>
  <div uk-grid>
    <div class="uk-width-expand">
      <h1>
        {{ $t('categories') }}
      </h1>
    </div>
    <div class="uk-width-1-1 uk-width-1-6@m">
      <div class="uk-flex uk-flex-right">
        <div v-if="canCreate">
          <router-link
            class="uk-icon-button uk-link-reset"
            :to="{ name : 'categories.create' }"
          >
            <i class="fas fa-plus"></i>
          </router-link>
        </div>
      </div>
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
