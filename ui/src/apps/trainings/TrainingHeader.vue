<template>
  <!-- eslint-disable max-len -->
  <div class="uk-grid">
    <div class="uk-width-1-1@s uk-width-5-6@m">
      <h1 class="uk-h1">{{ $t('training.events.title') }}</h1>
    </div>
    <div class="uk-width-1-1@s uk-width-1-6@m">
      <div class="uk-flex uk-flex-right">
        <div>
          <router-link v-if="training" class="uk-icon-button uk-link-reset"
            :to="browseLink">
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

import trainingStore from '@/stores/training';
import registerModule from '@/stores/mixin';

export default {
  i18n: messages,
  mixins: [
    registerModule(
      {
        training: trainingStore
      }
    ),
  ],
  computed: {
    training() {
      return this.$store.getters['training/training'](
        this.$route.params.id
      );
    },
    updateAllowed() {
      return this.training
        && this.$training.isAllowed('update', this.training);
    },
    updateLink() {
      return {
        name: 'trainings.update',
        params: {
          id: this.training.id }
      };
    },
    browseLink() {
      return {
        name: 'trainings.browse',
        params: {
          year: this.training.event.start_date.year(),
          month: this.training.event.start_date.month() + 1
        }
      };
    },
  }
};
</script>
