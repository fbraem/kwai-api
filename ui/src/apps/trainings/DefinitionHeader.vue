<template>
  <div class="uk-grid">
    <div class="uk-width-1-1@s uk-width-5-6@m">
      <h1 class="uk-h1">{{ $t('training.definitions.title') }}</h1>
      <h3 v-if="definition" class="uk-h3 uk-margin-remove">
        {{ definition.name }}
      </h3>
    </div>
    <div class="uk-width-1-1@s uk-width-1-6@m">
      <div class="uk-flex uk-flex-right">
        <div>
          <router-link class="uk-icon-button uk-link-reset"
            :to="{ name: 'trainings.definitions.browse' }">
            <i class="fas fa-list"></i>
          </router-link>
        </div>
        <div class="uk-margin-small-left">
          <router-link v-if="updateAllowed"
            class="uk-icon-button uk-link-reset" :to="updateLink">
            <i class="fas fa-edit"></i>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import messages from './lang';

export default {
  i18n: messages,
  computed: {
    definition() {
      return this.$store.getters['training/definition/definition'](
        this.$route.params.id
      );
    },
    updateAllowed() {
      return this.definition
        && this.$can('update', this.definition);
    },
    updateLink() {
      return {
        name: 'trainings.definitions.update',
        params: {
          id: this.definition.id }
      };
    }
  }
};
</script>
