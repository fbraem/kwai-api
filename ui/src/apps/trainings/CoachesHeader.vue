<template>
  <div uk-grid>
    <div class="uk-width-5-6">
      <h1>{{ $t('training.coaches.title') }}</h1>
    </div>
    <div class="uk-width-1-6">
      <div class="uk-flex uk-flex-right">
        <router-link v-if="$training_coach.isAllowed('create')"
          class="uk-icon-button uk-link-reset"
          :to="{ name : 'trainings.coaches.create' }">
          <i class="fas fa-plus"></i>
        </router-link>
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
