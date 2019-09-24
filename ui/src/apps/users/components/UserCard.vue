<template>
  <!-- eslint-disable max-len -->
  <div style="box-shadow: 0px 0px 20px 0 rgba(100, 100, 100, 0.3); display: flex; flex-direction: column;">
    <div style="display: flex;padding: 20px;">
      <img
        style="border-radius: 50%; width: 40px; height: 40px; margin: 20px;"
        :src="noAvatarImage"
      />
      <div>
        <h3>{{ user.name }}</h3>
        <p class="kwai-text-meta">
          <i class="fas fa-envelope"></i>&nbsp;
          <a :href="'mailto:' + user.email">{{ user.email }}</a>
        </p>
      </div>
    </div>
    <div style="padding: 20px;border-top: 1px solid var(--kwai-color-muted); display: flex; flex-direction: row; align-items: center;">
      <div>
        <strong>{{ $t('last_login') }} :</strong> {{ user.lastLoginFormatted }}
      </div>
      <div style="margin-left: auto;">
        <router-link
          class="kwai-icon-button"
          :to="userLink"
        >
          <i class="fas fa-ellipsis-h"></i>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import User from '@/models/users/User';

import messages from '../lang';

export default {
  i18n: messages,
  props: {
    user: {
      type: User,
      required: true
    }
  },
  computed: {
    noAvatarImage() {
      return require('@/apps/users/images/no_avatar.png');
    },
    userLink() {
      return {
        name: 'users.read',
        params: {
          id: this.user.id
        }
      };
    }
  }
};
</script>
