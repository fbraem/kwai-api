<template>
  <div uk-grid class="uk-light">
    <div class="uk-width-1-1">
      <h1 class="uk-margin-remove">{{ $t('news') }}</h1>
      <h3 v-if="creating" class="uk-margin-remove">{{ $t('create') }}</h3>
      <h3 v-else class="uk-margin-remove">{{ $t('update') }}</h3>
    </div>
  </div>
</template>

<script>
import messages from './lang';

export default {
  props: {
    creating: {
      type: Boolean,
      required: true
    }
  },
  i18n: messages
};
</script>
