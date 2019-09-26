<template>
  <Header
    :title="$t('pages')"
    :toolbar="toolbar"
  >
    <p>
      {{ $t('all_pages') }}
    </p>
  </Header>
</template>

<script>
import Page from '@/models/Page';
import messages from './lang';
import Header from '@/components/Header';
/**
 * Component for header of pages
 */
export default {
  components: {
    Header
  },
  i18n: messages,
  computed: {
    toolbar() {
      const buttons = [];
      if (this.$can('create', Page.type())) {
        buttons.push({
          icon: 'fas fa-plus',
          route: {
            name: 'pages.create'
          }
        });
      }
      return buttons;
    }
  }
};
</script>
