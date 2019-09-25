<template>
  <div class="hero-container">
    <div>
      <h1>
        {{ $t('members') }}
      </h1>
    </div>
    <div style="display:flex; align-items:flex-end;flex-flow:column">
      <router-link
        v-if="canUpload"
        class="secondary:kwai-icon-button"
        :to="{ name : 'members.upload' }"
      >
        <i class="fas fa-file-import"></i>
      </router-link>
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
