<template>
  <div>
    <div class="bg-red-700 border-b-8 border-gray-800">
      <div class="container mx-auto">
        <div class="flex flex-col">
          <div class="flex justify-between items-center sm:p-4 w-full">
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
        </div>
      </div>
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
