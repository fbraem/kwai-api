<template>
  <!-- eslint-disable max-len -->
  <div class="main-container">
    <div class="main-header" style="display: flex; justify-content: space-between; align-items: center;">
      <div style="font-size:1.5rem;margin-left:1rem">
        <a class="kwai-link-reset" href="/">
          {{ title }}
        </a>
      </div>
      <div style="display:flex;margin-right:5px">
        <a :href="facebook" class="primary:kwai-icon-button" style="margin-right:5px;">
          <i class="fab fa-facebook-f"></i>
        </a>
        <Login />
      </div>
    </div>
    <div class="main-hero" style="grid-area: main-hero">
      <router-view name="header"></router-view>
    </div>
    <div style="grid-area: main-content">
      <router-view name="main"></router-view>
    </div>
    <div class="main-footer" style="grid-column: main-footer">
      <router-view name="footer"></router-view>
    </div>
  </div>
</template>

<style scoped>
  .toolbar-container {
      display: grid;
      grid-template-columns: auto 1fr auto;
      grid-template-areas: "toolbar-left toolbar-middle toolbar-right";
      grid-gap: 1rem;
  }
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

  .main-header {
      grid-area: main-header;
      background-color: var(--kwai-color-primary-bg);
      color: var(--kwai-color-primary-fg);
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
