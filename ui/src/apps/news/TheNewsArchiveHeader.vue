<template>
  <!-- eslint-disable max-len -->
  <div class="hero-container">
    <div>
      <h1>
        {{ $t('news') }}
      </h1>
      <h3>
        {{ $t('archive_title', { monthName : monthName, year : year }) }}
      </h3>
    </div>
    <div style="display:flex; justify-content:flex-end;flex-flow:row;padding:5px">
      <router-link
        v-if="canCreate"
        class="kwai-icon-button kwai-theme-muted"
        :to="{ name : 'news.create' }"
      >
        <i class="fas fa-plus"></i>
      </router-link>
    </div>
  </div>
</template>

<script>
import Story from '@/models/Story';

import moment from 'moment';

import messages from './lang';

/**
 * Component for header of archive page
 */
export default {
  i18n: messages,
  computed: {
    canCreate() {
      return this.$can('create', Story.type());
    },
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
