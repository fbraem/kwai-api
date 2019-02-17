<template>
  <div>
    <h1>{{ $t('training.definitions.title') }}</h1>
    <h3 v-if="creating" class="uk-h3 uk-margin-remove">
      {{ $t('training.definitions.create') }}
    </h3>
    <h3 v-else class="uk-h3 uk-margin-remove">
      {{ $t('training.definitions.update') }}
    </h3>
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
