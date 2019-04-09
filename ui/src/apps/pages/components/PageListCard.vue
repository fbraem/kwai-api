<template>
  <div class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-media-top uk-cover-container">
      <img
        :src="require('@/apps/pages/images/road-sign-63983_1920.jpg')"
        alt=""
        uk-cover
      />
      <canvas height="150"></canvas>
    </div>
    <div class="uk-card-body">
      <h3 class="uk-card-title">
        {{ $t('pages') }}
      </h3>
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
