<template>
  <div class="uk-grid">
    <div class="uk-width-5-6">
      <h1 class="uk-h1 uk-margin-remove">{{ $t('members') }}</h1>
      <h3 class="uk-h3 uk-margin-remove">{{ $t('upload') }}</h3>
    </div>
    <div class="uk-width-1-6">
    </div>
  </div>
</template>

<script>
import messages from './lang';

export default {
  i18n: messages,
};
</script>
