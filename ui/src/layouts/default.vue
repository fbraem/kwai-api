<template>
    <v-app>
        <v-navigation-drawer fixed v-model="drawer" app>
        </v-navigation-drawer>
        <v-toolbar class="red" dark app>
            <v-toolbar-side-icon @click.stop="clickDrawer"></v-toolbar-side-icon>
            <v-toolbar-title>JudoKwai Kemzeke</v-toolbar-title>
            <v-spacer></v-spacer>
            <login></login>
            <v-btn icon href="/">
                <v-icon>home</v-icon>
            </v-btn>
        </v-toolbar>
        <v-content>
            <v-container grid-list-xl fluid>
                <router-view />
            </v-container>
        </v-content>
        <v-footer class="red" app>
            <span class="white--text">&copy; ClubMan</span>
        </v-footer>
    </v-app>
</template>

<script>
  import Login from '@/apps/auth/components/login.vue';

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
      methods : {
          clickDrawer() {
              this.drawer = !this.drawer;
          }
      }
  };
</script>
