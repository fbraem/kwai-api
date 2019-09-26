<template>
  <Header
    :title="$t('news')"
    :subtitle="$t('archive_title', { monthName : monthName, year : year })"
    :toolbar="toolbar"
  />
</template>

<script>
import Story from '@/models/Story';

import moment from 'moment';

import messages from './lang';

import Header from '@/components/Header';

/**
 * Component for header of archive page
 */
export default {
  components: {
    Header
  },
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
    },
    toolbar() {
      const buttons = [];
      if (this.$can('create', Story.type())) {
        buttons.push({
          icon: 'fas fa-plus',
          route: {
            name: 'news.create'
          }
        });
      }
      return buttons;
    }
  }
};
</script>
