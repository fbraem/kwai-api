<template>
  <Header
    :title="$t('members')"
    :toolbar="toolbar"
  />
</template>

<script>
import Member from '@/models/Member';
import Header from '@/components/Header';

import messages from './lang';
/**
 * Component for the header of members page
 */
export default {
  components: {
    Header
  },
  i18n: messages,
  computed: {
    toolbar() {
      const buttons = [];
      if (this.$can('upload', Member.type())) {
        buttons.push({
          icon: 'fas fa-file-import',
          route: {
            name: 'members.upload'
          }
        });
      }
      return buttons;
    }
  }
};
</script>
