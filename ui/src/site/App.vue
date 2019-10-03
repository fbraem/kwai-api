<template>
  <!-- eslint-disable max-len -->
  <div class="main-container">
    <div
      class="main-header flex justify-between items-center bg-red-700 sm:p-4"
      style="grid-area: main-header"
    >
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
    <div style="grid-area: main-hero">
      <router-view name="header"></router-view>
    </div>
    <div style="grid-area: main-content">
      <router-view name="main"></router-view>
    </div>
    <div
      class="main-footer"
      style="grid-column: main-footer"
    >
      <router-view name="footer"></router-view>
    </div>
  </div>
</template>

<style scoped>
  .main-container {
      display: grid;
      grid-template-columns: 1fr;
      grid-template-rows: 80px auto auto 100px;
      grid-template-areas:
          "main-header"
          "main-hero"
          "main-content"
          "main-footer"
      ;
  }
</style>

<script>
import Login from '@/apps/auth/components/Login.vue';

export default {
  components: {
    Login
  },
  data() {
    return {
      drawer: false
    };
  },
  computed: {
    title() {
      return this.$store.getters['title'];
    },
    subTitle() {
      return this.$store.getters['subTitle'];
    },
    facebook() {
      return this.$store.getters['facebook'];
    }
  },
  created() {
    document.title = this.title;
  },
  watch: {
    title(nv) {
      // document.title = nv;
    },
    '$route.meta.title': {
      handler(nv, ov) {
        if (nv) {
          document.title = this.title + ' - ' + nv;
        } else {
          document.title = this.title;
        }
      },
      immediate: true
    }
  },
  methods: {
    clickDrawer() {
      this.drawer = !this.drawer;
    }
  }
};
</script>
