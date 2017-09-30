<template>
    <site>
        <div slot="content">
            <v-layout row wrap>
                <v-flex xs12>
                    <h3>News</h3>
                </v-flex>
                <v-flex xs12 v-if="stories.length == 0">
                    No news for today
                </v-flex>
                <v-flex v-for="story in stories" :key="story.id" xs12 sm6 md4>
                    <v-card>
                        <v-card-title primary-title>
                            <div>
                                <h3 class="headline mb-0">{{ story.title }}</h3>
                                <div>
                                    {{ story.summary }}
                                </div>
                            </div>
                        </v-card-title>
                        <v-card-actions v-if="story.content != story.content.length > 0">
                            <v-btn :to="'/read/' + story.id" flat>Read more</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-flex>
            </v-layout>
        </div>
    </site>
</template>

<script>
  import Site from '@/site/components/site.vue';

  export default {
      components : {
          Site
      },
      computed : {
          stories() {
              return this.$store.state.newsModule.stories;
          }
      },
      mounted() {
        this.$store.dispatch('newsModule/browse')
          .catch((error) => {
            console.log(error);
        });
      },
      methods : {
      }
  };
</script>
