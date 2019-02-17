<template>
  <!-- eslint-disable max-len -->
  <div>
    <h1>{{ $t('training.events.title') }}</h1>
    <h3 v-if="creating" class="uk-h3 uk-margin-remove">{{ $t('training.events.create') }}</h3>
    <h3 v-else class="uk-h3 uk-margin-remove">{{ $t('training.events.update') }}</h3>
  </div>
</template>

<script>
import messages from './lang';

export default {
  i18n: messages,
  props: {
    creating: {
      type: Boolean,
      required: true
    }
  }
};
</script>
