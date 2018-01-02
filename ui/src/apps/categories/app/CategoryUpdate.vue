<template>
    <v-layout>
        <v-flex xs12 lg8 offset-lg2>
            <v-layout row wrap>
                <v-flex xs12>
                    <v-toolbar class="elevation-0">
                        <v-icon>add</v-icon>
                        <v-toolbar-title>Update Category</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-btn icon dark color="primary" @click="$router.go(-1)"><v-icon>cancel</v-icon></v-btn>
                    </v-toolbar>
                </v-flex>
                <v-flex xs12>
                    <CategoryForm :category="category" />
                </v-flex>
            </v-layout>
        </v-flex>
    </v-layout>
</template>

<script>
    import CategoryForm from './CategoryForm.vue';
    export default {
        components : {
            CategoryForm
        },
        data() {
            return {};
        },
        mounted() {
            this.fetchData(this.$route.params.id);
        },
        computed : {
            category() {
                return this.$store.getters['newsModule/category'](this.$route.params.id);
            }
        },
        beforeRouteUpdate(to, from, next) {
            this.fetchData(to.params.id);
            next();
        },
        methods : {
            fetchData(id) {
                this.$store.dispatch('newsModule/readCategory', {
                    id : id
                });
            }
        }

    };
</script>
