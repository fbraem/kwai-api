<template>
    <v-layout row wrap>
        <v-flex xs12>
            <v-toolbar class="elevation-0">
                <v-icon>mode_edit</v-icon>
                <v-toolbar-title>Update News <span v-if="story"> - {{ story.title }}</span></v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn icon dark color="primary" @click="$router.go(-1)"><v-icon>cancel</v-icon></v-btn>
            </v-toolbar>
        </v-flex>
        <v-flex xs12>
            <NewsForm :story="story"></NewsForm>
        </v-flex>
    </v-layout>
</template>

<script>
    import NewsForm from './NewsForm.vue';

    export default {
        components : {
            NewsForm
        },
        data() {
            return {};
        },
        mounted() {
            this.fetchData(this.$route.params.id);
        },
        computed : {
            story() {
                return this.$store.getters['newsModule/story'](this.$route.params.id);
            }
        },
        beforeRouteUpdate(to, from, next) {
            this.fetchData(to.params.id);
            next();
        },
        methods : {
            fetchData(id) {
                this.$store.dispatch('newsModule/read', {
                    id : id
                });
            }
        }
    };
</script>
