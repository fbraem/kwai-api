<template>
  <div class="hero-container">
    <div
      v-if="picture"
      style="grid-column: 1"
    >
      <img :src="picture" style="margin: auto; "/>
    </div>
    <div v-if="page">
      <div class="secondary:kwai-badge">
        <router-link :to="categoryLink">
          {{ page.category.name }}
        </router-link>
      </div>
      <h1>
        {{ page.content.title }}
      </h1>
      <p v-html="page.content.html_summary">
      </p>
      <div
        style="display: flex; justify-content: flex-end; flex-flow: row"
        class="kwai-buttons"
      >
        <router-link
          v-if="$can('update', page)"
          :to="{ name : 'pages.update', params : { id : page.id }}"
          class="secondary:kwai-icon-button"
        >
          <i class="fas fa-edit"></i>
        </router-link>
        <a v-if="$can('delete', page)"
          @click.prevent.stop="showModal"
          class="secondary:kwai-icon-button"
        >
          <i class="fas fa-trash"></i>
        </a>
      </div>
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
  </div>
</template>

<script>
import messages from './lang';

import AreYouSure from '@/components/AreYouSure.vue';

/**
 * Component for a page header
 */
export default {
  i18n: messages,
  components: {
    AreYouSure
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
    categoryLink() {
      return {
        name: 'pages.category',
        params: {
          category: this.page.category.id
        }
      };
    },
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
