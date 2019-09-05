<template>
  <div class="hero-container">
    <div>
      <h1>
        {{ $t('news') }}
      </h1>
      <h3 v-if="creating">
        {{ $t('create') }}
      </h3>
      <h3 v-else>
        {{ $t('update') }}
      </h3>
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
