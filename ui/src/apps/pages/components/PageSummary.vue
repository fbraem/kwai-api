<template>
  <!-- eslint-disable max-len -->
  <div class="kwai-page-card">
    <div
      v-if="page.header_overview_crop"
    >
      <img :src="page.header_overview_crop" />
    </div>
    <div>
      <h5>
        {{ page.content.title }}
      </h5>
    </div>
    <div
      v-html="page.content.html_summary"
      style="flex-grow: 1;"
    >
    </div>
    <div style="align-self:flex-end;">
      <router-link
        class="kwai-icon-button"
        :to="contentLink"
      >
        <i class="fas fa-ellipsis-h"></i>
      </router-link>
    </div>
  </div>
</template>

<script>
import Page from '@/models/Page';

/**
 * Component for showing a summary card of an information page
 */
export default {
  props: {
    /**
     * The page for the summary
     */
    page: {
      type: Page,
      required: true
    }
  },
  computed: {
    contentLink() {
      return {
        name: 'pages.read',
        params: {
          id: this.page.id
        }
      };
    },
  }
};
</script>
