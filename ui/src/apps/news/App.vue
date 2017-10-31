<template>
    <v-card flat>
        <v-card-title primary-title>
            <div class="headline">News</div>
            <v-spacer></v-spacer>
            <v-btn v-if="$isAllowed('create')" icon dark color="primary" :to="'/create'"><v-icon>add</v-icon></v-btn>
        </v-card-title>
        <v-card-text>
            <v-layout row wrap>
                <v-flex xs12 sm2>
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
                <v-flex xs12 sm10>
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
