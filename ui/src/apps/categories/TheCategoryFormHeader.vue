<template>
  <div class="hero-container">
    <div>
      <h1>
        {{ $t('category') }}
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
  props: {
    /**
     * Is the form for creating or updating a category?
     */
    creating: {
      type: Boolean,
      required: true
    }
  },
  i18n: messages
};
</script>
