<template>
  <div>
    <h1 class="uk-margin-remove">{{ $t('page') }}</h1>
    <p>
      {{ $t('all_pages') }}
    </p>
  </div>
</template>

<script>
import messages from './lang';

export default {
  i18n: messages
};
</script>
