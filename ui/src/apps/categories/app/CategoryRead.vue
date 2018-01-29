<template>
    <v-layout row wrap>
        <v-toolbar v-if="category" class="elevation-0">
            <v-icon>subject</v-icon>
            <v-toolbar-title>News - {{ category.name }}</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon :to="{ name : 'categories.update', params : {id : category.id }}"><v-icon>edit</v-icon></v-btn>
        </v-toolbar>
        <v-flex v-if="category" xs12>
            <v-card>
                <v-card-title>
                    <div>
                        {{ category.description }}
                    </div>
                </v-card-title>
            </v-card>
        </v-flex>
    </v-layout>
</template>

<script>
    export default {
        computed : {
            category() {
                return this.$store.getters['categoryModule/category'](this.$route.params.id);
            }
        },
        mounted() {
          this.$store.dispatch('categoryModule/read', { id : this.$route.params.id })
            .catch((error) => {
              console.log(error);
          });
        }
    };
</script>
