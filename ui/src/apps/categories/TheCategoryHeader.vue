<template>
  <Header
    v-if="category"
    :title="category.name"
    :toolbar="toolbar"
    :picture="picture"
    :logo="category.icon_picture"
  >
    <div v-html="category.description">
    </div>
  </Header>
</template>

<script>
import Header from '@/components/Header';

import Category from '@/models/Category';

import messages from './lang';

/**
 * Component for header of category page
 */
export default {
  components: {
    Header
  },
  i18n: messages,
  computed: {
    category() {
      return this.$store.getters['category/category'](this.$route.params.id);
    },
    picture() {
      if (this.category) return this.category.header_picture;
      return null;
    },
    toolbar() {
      const buttons = [];
      if (this.$can('create', Category.type())) {
        buttons.push({
          icon: 'fas fa-plus',
          route: {
            name: 'categories.create'
          }
        });
      }
      if (this.$can('update', this.category)) {
        buttons.push({
          icon: 'fas fa-edit',
          route: {
            name: 'categories.update',
            params: {
              id: this.category.id
            }
          }
        });
      }
      return buttons;
    }
  }
};
</script>
