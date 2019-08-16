<template>
  <!-- eslint-disable max-len -->
  <div class="uk-offcanvas-content">
    <div class="main-container">
      <div class="main-header toolbar-container">
        <div style="grid-area:toolbar-left;font-size:1.5rem;margin-left:1rem;align-self:center">
          <a class="kwai-link-reset" href="/">
            {{ title }}
          </a>
        </div>
        <div style="grid-area:toolbar-middle">
        </div>
        <div style="grid-area:toolbar-right;align-self:center;display:flex;margin-right:5px">
          <a :href="facebook" class="kwai-icon-button" style="margin-right:5px;">
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
  </div>
</template>

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
      document.title = nv;
    }
  },
  methods: {
    clickDrawer() {
      this.drawer = !this.drawer;
    }
  }
};
</script>
