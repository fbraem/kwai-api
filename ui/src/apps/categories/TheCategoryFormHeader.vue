<template>
  <div uk-grid class="uk-light">
    <div class="uk-width-1-1">
      <h1 class="uk-margin-remove">
        {{ $t('category') }}
      </h1>
      <h3
        v-if="creating"
        class="uk-margin-remove"
      >
        {{ $t('create') }}
      </h3>
      <h3
        v-else
        class="uk-margin-remove"
      >
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
