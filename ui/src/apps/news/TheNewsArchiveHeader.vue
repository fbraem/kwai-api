<template>
  <ImageHeader
    :title="$t('news')"
    :toolbar="toolbar"
    :pictures="pictures"
  >
    <p>
      {{ $t('archive_title', { monthName : monthName, year : year }) }}
    </p>
  </ImageHeader>
</template>

<script>
import Story from '@/models/Story';

import moment from 'moment';

import messages from './lang';

import ImageHeader from '@/components/ImageHeader';

/**
 * Component for header of archive page
 */
export default {
  components: {
    ImageHeader
  },
  i18n: messages,
  data() {
    return {
      pictures: {
        '1024w': require('./images/archive_lg.jpg'),
        '768w': require('./images/archive_md.jpg'),
        '640w': require('./images/archive_sm.jpg')
      }
    };
  },
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
