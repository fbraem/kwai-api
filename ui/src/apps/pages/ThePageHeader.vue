<template>
  <component
    :is="header"
    v-if="page"
    :title="page.content.title"
    :toolbar="toolbar"
    :badge="badge"
    :picture="picture"
    :pictures="pictures"
  >
    <div v-html="page.content.html_summary">
    </div>
    <AreYouSure
      :show="isModalVisible"
      :yes="$t('delete')"
      :no="$t('cancel')"
      @sure="deletePage"
      @close="close"
    >
      {{ $t('are_you_sure') }}
    </AreYouSure>
  </component>
</template>

<script>
import messages from './lang';

import AreYouSure from '@/components/AreYouSure.vue';
import Header from '@/components/Header.vue';
import ImageHeader from '@/components/ImageHeader.vue';

/**
 * Component for a page header
 */
export default {
  i18n: messages,
  components: {
    AreYouSure,
    Header,
    ImageHeader
  },
  data() {
    return {
      isModalVisible: false
    };
  },
  computed: {
    page() {
      return this.$store.state.page.active;
    },
    picture() {
      return this.page.picture;
    },
    pictures() {
      const pictures = {};
      if (this.page.images.crop_lg) {
        pictures[this.page.images.crop_lg] = '1024w';
      }
      if (this.page.images.crop_md) {
        pictures[this.page.images.crop_md] = '768w';
      }
      if (this.page.images.crop_sm) {
        pictures[this.page.images.crop_sm] = '640w';
      }
      return pictures;
    },
    header() {
      if (this.picture) return 'ImageHeader';
      return 'Header';
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
        name: this.page.category.name,
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
