<template>
  <Header
    v-if="category"
    :title="$t('news')"
    :subtitle="category.name"
    :picture="picture"
    :toolbar="toolbar"
  >
    <div v-html="category.description"></div>
  </Header>
</template>

<script>
import Story from '@/models/Story';
import messages from './lang';
import Header from '@/components/Header';
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
      /* eslint-disable max-len */
      if (this.$route.params.category) {
        return this.$store.getters['category/category'](this.$route.params.category);
      }
      return null;
      /* eslint-enable max-len */
    },
    picture() {
      if (this.category && this.category.images) {
        return this.category.images.normal;
      }
      return null;
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
