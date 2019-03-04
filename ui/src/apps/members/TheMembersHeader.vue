<template>
  <div class="uk-grid">
    <div class="uk-width-5-6">
      <h1>
        {{ $t('members') }}
      </h1>
    </div>
    <div class="uk-width-1-6">
      <div class="uk-flex uk-flex-right">
        <div class="uk-margin-small-left">
          <router-link
            v-if="canUpload"
            class="uk-icon-button uk-link-reset"
            :to="{ name : 'members.upload' }"
          >
            <i class="fas fa-file-import"></i>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Member from '@/models/Member';

import messages from './lang';
/**
 * Component for the header of members page
 */
export default {
  i18n: messages,
  computed: {
    canUpload() {
      return this.$can('upload', Member.type());
    }
  }
};
</script>
