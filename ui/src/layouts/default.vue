<template>
    <v-app>
        <v-navigation-drawer persistent v-model="drawer" light enable-resize-watcher overflow app>
        </v-navigation-drawer>
        <v-toolbar class="indigo" dark app>
            <v-toolbar-side-icon @click.stop="clickDrawer"></v-toolbar-side-icon>
            <v-toolbar-title>JudoKwai Kemzeke</v-toolbar-title>
            <v-spacer></v-spacer>
            <login></login>
        </v-toolbar>
        <main>
            <v-content>
                <v-container grid-list-lg fluid>
                    <router-view />
                </v-container>
            </v-content>
        </main>
        <v-footer class="indigo">
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
