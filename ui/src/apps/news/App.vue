<template>
    <v-card flat>
        <v-card-media :src="backgroundImage" height="200">
            <v-container fill-height fluid>
                <v-layout fill-height>
                    <v-flex xs12 align-end flexbox>
                        <span class="pa-2 white headline"><v-icon class="mr-2">subject</v-icon>News</span>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-card-media>
        <v-card-text>
            <v-layout row wrap>
                <v-flex xs12 sm4 md3>
                    <v-layout row wrap>
                        <v-flex xs12>
                            <v-card>
                                <v-card-title>
                                    <div class="headline">Categories</div>
                                </v-card-title>
                            </v-card>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex xs12>
                            <v-card>
                                <v-card-title>
                                    <div class="headline">Archive</div>
                                </v-card-title>
                            </v-card>
                        </v-flex>
                    </v-layout>
                </v-flex>
                <v-flex xs12 sm8 md9>
                    <v-layout v-if="noNews" row wrap>
                        <v-flex xs12>
                            No news for today
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex v-for="story in stories" :key="story.id" xs12 sm6 md5 lg4>
                            <NewsCard :story="story" :complete="false" />
                        </v-flex>
                    </v-layout>
                </v-flex>
            </v-layout>
            <v-layout>
                <v-flex xs12>
                    <v-btn v-if="$isAllowed('create')" icon :to="'create'">
                        <v-icon>add</v-icon>
                    </v-btn>
                </v-flex>
            </v-layout>
        </v-card-text>
    </v-card>
</template>

<script>
    import NewsCard from './components/NewsCard.vue';

    export default {
        components : {
            NewsCard
        },
        data() {
            return {
                category : null,
                year : null,
                month : null
            };
        },
        computed : {
            stories() {
                return this.$store.state.newsModule.stories;
            },
            noNews() {
                return !this.stories || this.stories.length == 0;
            },
            categories() {
                return this.$store.getters['newsModule/categories'](this.$route.params.id);
            },
            backgroundImage() {
                return require('./images/news.jpg');
            }
          },
          mounted() {
              this.fetchData();
          },
          methods : {
              fetchData() {
                  this.$store.dispatch('newsModule/browse', this.$route.params);
                  this.year = this.$route.params.year;
                  this.month = this.$route.params.month;
                  this.year = this.$route.params.year;
              }
          }
      };
</script>
