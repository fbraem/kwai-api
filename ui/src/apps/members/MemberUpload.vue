<template>
  <div uk-grid>
    <div>
      <div ref="upload" class="uk-placeholder uk-text-center">
        <i class="uk-text-middle fas fa-cloud-upload-alt fa-2x uk-margin-right">
        </i>
        <span class="uk-text-middle">
          Attach binaries by dropping them here or
        </span>
        <div uk-form-custom>
          <input
            type="file"
            multiple
          />
          <span class="uk-link">
            selecting one
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import tokenStore from '@/js/TokenStore';

import UIkit from 'uikit';

import messages from './lang';

/**
 * Page for uploading members file
 */
export default {
  i18n: messages,
  mounted() {
    UIkit.upload(this.$refs.upload, {
      url: '/api/sport/judo/members/upload',
      multiple: false,
      name: 'csv',
      beforeSend(env) {
        env.headers['Authorization'] = `Bearer ${tokenStore.access_token}`;
      },
      complete(e) {
        var data = JSON.parse(e.response).data;
        console.log(data);
      }
    });
  }
};
</script>
