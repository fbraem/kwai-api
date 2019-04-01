<template>
  <div class="uk-grid-small uk-flex uk-flex-middle" uk-grid>
    <div class="uk-width-expand">
      <router-link
        :to="pageLink"
        class="uk-link-reset"
      >
        <h6 class="uk-h6 uk-margin-small-bottom">
          {{ page.content.title }}
        </h6>
        <div class="uk-text-meta uk-text-truncate">
          {{ page.content.summary }}
        </div>
      </router-link>
    </div>
    <div class="uk-width-auto">
      <router-link
        :to="pageLink"
        class="uk-icon-button uk-link-reset"
      >
        <i class="fas fa-ellipsis-h"></i>
      </router-link>
    </div>
  </div>
</template>

<script>
import Page from '@/models/Page';

export default {
  props: {
    page: {
      type: Page,
      required: true
    }
  },
  computed: {
    pageLink() {
      return {
        name: 'pages.read',
        params: {
          id: this.page.id
        }
      };
    }
  }
};
</script>
