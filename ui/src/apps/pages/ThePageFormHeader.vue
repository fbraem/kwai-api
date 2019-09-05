<template>
  <div class="hero-container">
    <div>
      <h1>
        {{ $t('page') }}
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

/**
 * Component for form header
 */
export default {
  i18n: messages,
  props: {
    /**
     * Creating or updating a page?
     */
    creating: {
      type: Boolean,
      required: true
    }
  }
};
</script>
