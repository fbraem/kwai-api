<template>
  <Header
    :title="$t('news')"
    :toolbar="toolbar"
  >
    <p>
      {{ $t('all_news') }}
    </p>
  </Header>
</template>

<script>
import Story from '@/models/Story';
import messages from './lang';
import Header from '@/components/Header';

export default {
  components: {
    Header
  },
  i18n: messages,
  computed: {
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
