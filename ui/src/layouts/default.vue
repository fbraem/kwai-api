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
        <router-view />
        <section class="uk-section uk-section-small uk-container uk-container-expand uk-section-secondary">
            <div uk-grid>
                <div>
                    <a class="uk-link-reset" href="/">&copy; Judokwai Kemzeke</a>
                </div>
                <div class="uk-width-expand">
                    <span class="uk-float-right">
                        <a href="mailto:franky.braem@gmail.com" class="uk-icon-button uk-margin-small-right uk-link-reset">
                            <i class="fas fa-envelope"></i>
                        </a>
                    </span>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
  import Login from '@/apps/auth/components/Login.vue';

  import config from 'config';

  export default {
      name : 'default',
      components : {
          Login
      },
      data() {
          return {
              drawer : false
          };
      },
      computed : {
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
      watch : {
          title(nv) {
              document.title = nv;
          }
      },
      methods : {
          clickDrawer() {
              this.drawer = !this.drawer;
          }
      }
  };
</script>
