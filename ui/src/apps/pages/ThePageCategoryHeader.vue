<template>
  <Header
    v-if="category"
    :title="category.name"
    :subtitle="$t('page')"
    :toolbar="toolbar"
  >
    <p>
      {{ category.description }}
    </p>
  </Header>
</template>

<script>
import Page from '@/models/Page';

import messages from './lang';

import Header from '@/components/Header';
/**
 * Component for category header
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
    },
    category() {
      /* eslint-disable max-len */
      return this.$store.getters['category/category'](this.$route.params.category);
    },
    picture() {
      if (this.category && this.category.images) {
        return this.category.images.normal;
      }
      return null;
    },
    grid() {
      return this.picture ? '2' : '1 / 3';
    }
  }
};
</script>
