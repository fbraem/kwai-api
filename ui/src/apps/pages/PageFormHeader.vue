<template>
  <div>
    <h1 class="uk-margin-remove">{{ $t('page') }}</h1>
    <h3 v-if="creating" class="uk-margin-remove">{{ $t('create') }}</h3>
    <h3 v-else class="uk-margin-remove">{{ $t('update') }}</h3>
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
