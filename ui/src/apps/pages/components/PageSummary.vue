<template>
  <!-- eslint-disable max-len -->
  <div>
    <div class="uk-card uk-card-small uk-card-default">
      <div
        v-if="page.header_overview_crop"
        class="uk-card-media"
      >
        <div class="uk-inline-clip">
          <img :src="page.header_overview_crop" />
        </div>
      </div>
      <div class="uk-card-body">
        <h5 class="uk-margin-small-bottom uk-margin-remove-adjacent uk-text-bold">
          {{ page.title }}
        </h5>
        <p
          class="uk-text-small uk-text-muted"
          v-html="page.summary"
        >
        </p>
        <span class="uk-float-right">
          <router-link
            class="uk-icon-button uk-link-reset"
            :to="contentLink"
          >
            <i class="fas fa-ellipsis-h"></i>
          </router-link>
        </span>
      </div>
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
