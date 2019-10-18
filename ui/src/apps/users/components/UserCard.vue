<template>
  <!-- eslint-disable max-len -->
  <div class="shadow-lg flex flex-col">
    <div class="flex p-3">
      <img
        class="rounded-full h-16 w-16 m-3"
        :src="noAvatarImage"
      />
      <div>
        <h3>{{ user.name }}</h3>
        <p class="text-sm">
          <i class="fas fa-envelope"></i>&nbsp;
          <a :href="'mailto:' + user.email">{{ user.email }}</a>
        </p>
      </div>
    </div>
    <div class="p-3 border-t border-gray-300 flex align-center">
      <div>
        <strong>{{ $t('last_login') }} :</strong> {{ user.lastLoginFormatted }}
      </div>
      <div class="ml-auto">
        <router-link
          class="icon-button text-gray-700 hover:bg-gray-300"
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
