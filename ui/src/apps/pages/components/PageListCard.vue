<template>
  <!-- eslint-disable max-len -->
  <div style="border: 1px solid #d3d3d3; border-radius: .25rem;">
    <div :style="'background-size: cover; min-height:150px; background-position: center; background-color: #ccc; background-image:url(' + require('@/apps/pages/images/road-sign-63983_1920.jpg') + ')'">
    </div>
    <div style="padding:15px">
      <h3>{{ $t('pages') }}</h3>
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
