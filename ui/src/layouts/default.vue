<template>
    <v-app>
        <!--
        <v-navigation-drawer fixed v-model="drawer" app>
        </v-navigation-drawer>
        -->
        <v-toolbar class="red" dark app>
            <!--
            <v-toolbar-side-icon @click.stop="clickDrawer"></v-toolbar-side-icon>
            -->
            <v-btn icon href="/">
                <v-icon>home</v-icon>
            </v-btn>
            <v-toolbar-title>
                {{ title }}
                <span v-if="subTitle && subTitle.length > 0"> &bull; {{ subTitle }}</span>
            </v-toolbar-title>
            <v-spacer></v-spacer>
            <login></login>
        </v-toolbar>
        <v-content>
            <router-view />
        </v-content>
        <v-footer class="red" app dark>
            <span class="white--text">&copy; Judokwai Kemzeke</span>
            <v-spacer></v-spacer>
            <v-btn icon href="mailto:franky.braem@gmail.com">
                <v-icon>fa-envelope</v-icon>
            </v-btn>
        </v-footer>
    </v-app>
</template>

<script>
  import Login from '@/apps/auth/components/login.vue';

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
