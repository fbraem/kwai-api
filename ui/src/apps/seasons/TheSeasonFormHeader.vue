<template>
  <div class="hero-container">
    <h1>
      {{ $t('seasons') }}
    </h1>
    <h3 v-if="creating">
      {{ $t('create') }}
    </h3>
    <h3 v-else>
      {{ $t('update') }}
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
