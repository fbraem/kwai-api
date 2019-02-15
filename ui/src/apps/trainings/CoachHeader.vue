<template>
  <div uk-grid>
    <div class="uk-width-5-6">
      <h1>{{ $t('training.coaches.title') }}</h1>
      <h3 v-if="coach" class="uk-h3 uk-margin-remove">
        {{ coach.name }}
      </h3>
    </div>
    <div class="uk-width-1-1@s uk-width-1-6@m">
      <div class="uk-flex uk-flex-right">
        <div>
          <router-link class="uk-icon-button uk-link-reset"
            :to="{ name: 'trainings.coaches' }">
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
import coachStore from '@/stores/training/coaches';
import registerModule from '@/stores/mixin';

export default {
  i18n: messages,
  mixins: [
    registerModule(
      {
        training: trainingStore
      },
      {
        training: trainingStore,
        coach: coachStore,
      }
    ),
  ],
  computed: {
    coach() {
      return this.$store.getters['training/coach/coach'](
        this.$route.params.id
      );
    },
    updateAllowed() {
      return this.coach
        && this.$training_coach.isAllowed('update', this.coach);
    },
    updateLink() {
      return {
        name: 'trainings.coaches.update',
        params: {
          id: this.coach.id }
      };
    },
  }
};
</script>
