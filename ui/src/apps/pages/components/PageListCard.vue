<template>
  <div class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header">
      <h3 class="uk-card-title">{{ $t('pages') }}</h3>
    </div>
    <div class="uk-card-body">
      <PageSummaryList :pages="pages" />
    </div>
  </div>
</template>

<script>
import messages from '../lang';
import PageSummaryList from './PageSummaryList';

export default {
  props: {
    pages: {
      type: Array,
      required: true
    }
  },
  components: {
    PageSummaryList
  },
  i18n: messages
};
</script>
