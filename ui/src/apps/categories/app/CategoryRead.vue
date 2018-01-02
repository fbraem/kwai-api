<template>
    <v-layout>
        <v-flex xs12 lg8 offset-lg2>
            <v-layout v-if="category" row wrap>
                <v-flex xs12>
                    <v-toolbar class="elevation-0">
                        <v-icon>subject</v-icon>
                        <v-toolbar-title>News - {{ category.name }}</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-btn icon :to="'/category/update/' + category.id"><v-icon>edit</v-icon></v-btn>
                    </v-toolbar>
                </v-flex>
                <v-flex xs12>
                    <v-card>
                        <v-card-title>
                            <div>
                                {{ category.description }}
                            </div>
                        </v-card-title>
                    </v-card>
                </v-flex>
            </v-layout>
        </v-flex>
    </v-layout>
</template>

<script>
    export default {
        computed : {
            category() {
                return this.$store.getters['newsModule/category'](this.$route.params.id);
            }
        },
        mounted() {
          this.$store.dispatch('newsModule/readCategory', { id : this.$route.params.id })
            .catch((error) => {
              console.log(error);
          });
        }
    };
</script>
