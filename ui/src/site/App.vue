<template>
  <div class="uk-offcanvas-content">
    <div class="uk-container uk-container-expand uk-light" style="background-color:#c61c18">
      <nav class="uk-navbar">
        <div class="uk-navbar-left">
          <a class="uk-navbar-item uk-logo" href="/">{{ title }}</a>
          <span v-if="subTitle && subTitle.length > 0"> &bull; {{ subTitle }}</span>
        </div>
        <div class="uk-navbar-right">
          <a :href="facebook" class="uk-icon-button uk-link-reset">
            <i class="fab fa-facebook-f"></i>
          </a>
          <Login />
        </div>
      </nav>
    </div>
    <section class="uk-section uk-section-small uk-section-secondary uk-container uk-container-expand">
      <router-view name="header"></router-view>
    </section>
    <section class="uk-section uk-section-small uk-container uk-container-expand">
      <router-view name="main"></router-view>
    </section>
    <section class="uk-section uk-section-small uk-container uk-container-expand uk-section-secondary">
      <router-view name="footer"></router-view>
    </section>
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
