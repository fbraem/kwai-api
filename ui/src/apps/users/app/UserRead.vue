<template>
    <v-container fluid grid-list-xl :class=" { 'pa-2' : $vuetify.breakpoint.name == 'xs' }">
        <v-layout v-if="user" row wrap>
            <v-flex xs12 sm6 md4>
                <v-card>
                    <v-card-text>
                        <v-layout column>
                            <v-flex xs12 class="text-xs-center pa-1">
                                <img :src="noAvatarImage" style="border-radius:50%;width:150px;height:150px"/>
                            </v-flex>
                            <v-flex xs12 v-if="user.first_name || user.last_name" class="text-xs-center pa-1">
                                {{ user.first_name }}&nbsp;{{ user.last_name }}
                            </v-flex>
                            <v-flex xs12 class="text-xs-center pa-1">
                                {{ user.email }}
                            </v-flex>
                            <v-divider></v-divider>
                            <v-card-actions>
                                <v-btn icon :href="'mailto:' + user.email">
                                    <v-icon>fa-envelope</v-icon>
                                </v-btn>
                            </v-card-actions>
                        </v-layout>
                    </v-card-text>
                </v-card>
            </v-flex>
            <v-flex xs12 sm6 md8>
                <v-card>
                    <v-card-title primary-title>
                        <div>
                            <h3 class="headline mb-0">{{ $t('profile') }}</h3>
                        </div>
                    </v-card-title>
                </v-card>
            </v-flex>
        </v-layout>
        <v-layout>
            <v-flex xs12 sm6>
                <v-card>
                    <v-card-title>
                        <div class="headline mb-0">
                            Your Newsstories
                        </div>
                    </v-card-title>
                    <v-card-text v-if="stories">
                        <div v-if="stories.length == 0">
                            No news stories
                        </div>
                        <v-list v-else two-line>
                            <template v-for="story in stories">
                                <v-divider></v-divider>
                                <v-list-tile :key="story.id" @click="showNews(story.id)">
                                    <v-list-tile-content>
                                        <v-list-tile-title>{{ story.contents[0].title}}</v-list-tile-title>
                                        <v-list-tile-sub-title v-html="story.contents[0].summary"></v-list-tile-sub-title>
                                    </v-list-tile-content>
                                </v-list-tile>
                            </template>
                        </v-list>
                    </v-card-text>
                </v-card>
            </v-flex>
            <v-flex xs12 sm6>
                <v-card>
                    <v-card-title>
                        <div class="headline mb-0">
                            Your pages
                        </div>
                    </v-card-title>
                    <v-card-text v-if="pages">
                        <div v-if="pages.length == 0">
                            No pages
                        </div>
                        <v-list v-else two-line>
                            <template v-for="page in pages">
                                <v-divider></v-divider>
                                <v-list-tile :key="page.id" @click="showPage(page.id)">
                                    <v-list-tile-content>
                                        <v-list-tile-title>{{ page.contents[0].title}}</v-list-tile-title>
                                        <v-list-tile-sub-title v-html="page.contents[0].summary"></v-list-tile-sub-title>
                                    </v-list-tile-content>
                                </v-list-tile>
                            </template>
                        </v-list>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    import messages from '../lang/UserRead';

    import NewsStore from '@/apps/news/store.js'
    import PageStore from '@/apps/pages/store.js'

    export default {
        i18n : {
            messages
        },
        computed : {
            user() {
                return this.$store.getters['userModule/user'](this.$route.params.id);
            },
            stories() {
                return this.$store.getters['newsModule/stories'];
            },
            pages() {
                return this.$store.getters['pageModule/pages'];
            },
            noAvatarImage() {
                return require('@/apps/users/images/no_avatar.png');
            },
            loading() {
                return this.$store.getters['userModule/loading'];
            }
        },
        created() {
            if (!this.$store.state.newsModule) {
                this.$store.registerModule('newsModule', NewsStore);
            }
            if (!this.$store.state.pageModule) {
                this.$store.registerModule('pageModule', PageStore);
            }
        },
        mounted() {
          this.$store.dispatch('userModule/read', { id : this.$route.params.id })
            .catch((error) => {
              console.log(error);
          });
          this.$store.dispatch('newsModule/browse', {
              user : this.$route.params.id
           }).catch((error) => {
              console.log(error);
          });
          this.$store.dispatch('pageModule/browse', {
              user : this.$route.params.id
           }).catch((error) => {
              console.log(error);
          });
      },
      methods : {
          showNews(id) {
              this.$router.push({ name : 'news.story', params : { id : id }});
          },
          showPage(id) {
              this.$router.push({ name : 'pages.read', params : { id : id }});
          }
      }
    };
</script>
