<template>
  <Header v-if="page"
    :picture="picture"
    :title="page.content.title"
    :toolbar="toolbar"
    :badge="badge"
  >
    <div v-html="page.content.html_summary">
    </div>
    <AreYouSure
      v-show="isModalVisible"
      :yes="$t('delete')"
      :no="$t('cancel')"
      @sure="deletePage"
      @close="close"
    >
      {{ $t('are_you_sure') }}
    </AreYouSure>
  </Header>
</template>

<script>
import messages from './lang';

import AreYouSure from '@/components/AreYouSure.vue';
import Header from '@/components/Header.vue';

/**
 * Component for a page header
 */
export default {
  i18n: messages,
  components: {
    AreYouSure,
    Header
  },
  data() {
    return {
      isModalVisible: false
    };
  },
  computed: {
    page() {
      return this.$store.getters['page/page'](this.$route.params.id);
    },
    picture() {
      if (this.page) return this.page.picture;
      return null;
    },
    categoryRoute() {
      return {
        name: 'pages.category',
        params: {
          category: this.page.category.id
        }
      };
    },
    badge() {
      return {
        title: this.page.category.name,
        route: this.categoryRoute
      };
    },
    toolbar() {
      const buttons = [];
      if (this.page) {
        if (this.$can('update', this.page)) {
          buttons.push({
            icon: 'fas fa-edit',
            route: {
              name: 'pages.update',
              params: {
                id: this.page.id
              }
            }
          });
          if (this.$can('delete', this.page)) {
            buttons.push({
              icon: 'fas fa-trash',
              method: this.showModal
            });
          }
        }
      }
      return buttons;
    }
  },
  methods: {
    showModal() {
      this.isModalVisible = true;
    },
    close() {
      this.isModalVisible = false;
    },
    deletePage() {
      this.isModalVisible = false;
      console.log('delete');
    }
  }
};
</script>
