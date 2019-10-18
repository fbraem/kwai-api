<template>
  <div class="h-full shadow-lg p-2 flex flex-col">
    <div v-if="page.header_overview_crop">
      <img :src="page.header_overview_crop" />
    </div>
    <div>
      <h5>
        {{ page.content.title }}
      </h5>
    </div>
    <div
      class="text-sm"
      v-html="page.content.html_summary"
    >
    </div>
    <div class="self-end">
      <router-link
        class="icon-button text-gray-700 hover:bg-gray-300"
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
