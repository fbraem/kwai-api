<template>
    <site>
        <div slot="content">
            <v-layout row wrap>
                <v-flex xs12>
                    <v-toolbar class="elevation-0">
                        <v-toolbar-title>News</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-btn v-if="$isAllowed('create')" icon dark class="primary" :to="'/create'"><v-icon>add</v-icon></v-btn>
                    </v-toolbar>
                </v-flex>
            </v-layout>
            <v-layout v-if="stories.length == 0" row wrap>
                <v-flex xs12>
                    No news for today
                </v-flex>
            </v-layout>
            <v-layout row wrap>
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
                        <v-card-actions>
                            <v-btn v-if="story.content != story.content.length > 0" icon :to="'/read/' + story.id" flat>
                                <v-icon>more_horiz</v-icon>
                            </v-btn>
                            <v-spacer></v-spacer>
                            <v-btn v-if="$isAllowed('update', story)" icon :to="'/update/' + story.id" flat>
                                <v-icon>mode_edit</v-icon>
                            </v-btn>
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
