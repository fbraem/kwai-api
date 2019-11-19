<template>
  <div class="flex justify-between items-center bg-red-700 sm:p-4">
    <div class="text-xl ml-2">
      <a class="no-underline text-red-300 hover:no-underline fond-bold"
        href="/"
      >
        {{ title }}
      </a>
    </div>
    <div class="flex mr-1">
      <a
        class="icon-button text-red-300 hover:bg-red-900 mr-1"
        :href="facebook"
      >
        <i class="fab fa-facebook-f"></i>
      </a>
      <Login />
    </div>
  </div>
</template>

<script>
import Login from '@/apps/auth/components/Login.vue';

export default {
  components: {
    Login
  },
  computed: {
    title() {
      return this.$store.state.site?.page.title;
    },
    subTitle() {
      return this.$store.state.site?.page.subTitle;
    },
    facebook() {
      return this.$store.state.site?.facebook;
    }
  },
};
</script>
