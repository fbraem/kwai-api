<template>
  <div uk-grid class="uk-light">
    <div class="uk-width-1-1 uk-width-5-6@m">
      <h1 class="uk-margin-remove">{{ $t('news') }}</h1>
      <h3 class="uk-margin-remove">
        {{ $t('archive_title', { monthName : monthName, year : year }) }}
      </h3>
    </div>
    <div class="uk-width-1-1 uk-width-1-6@m">
      <div class="uk-flex uk-flex-right">
        <router-link v-if="$story.isAllowed('create')"
          class="uk-icon-button uk-link-reset" :to="{ name : 'news.create' }">
          <i class="fas fa-plus"></i>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment';

import messages from './lang';

export default {
  i18n: messages,
  computed: {
    year() {
      return this.$route.params.year;
    },
    month() {
      return this.$route.params.month;
    },
    monthName() {
      return moment.months()[this.month - 1];
    }
  }
};
</script>
