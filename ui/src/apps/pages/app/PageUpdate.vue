<template>
    <v-container>
        <v-layout>
            <v-flex xs12 lg10 offset-lg1>
                <v-layout row wrap>
                    <v-flex xs12>
                        <v-toolbar class="elevation-0">
                            <v-icon>fa-edit</v-icon>
                            <v-toolbar-title>{{ $t('update') }} <span v-if="page"> - {{ page.title }}</span></v-toolbar-title>
                            <v-spacer></v-spacer>
                            <v-btn icon dark color="primary" @click="$router.go(-1)"><v-icon>fa-times</v-icon></v-btn>
                        </v-toolbar>
                    </v-flex>
                    <v-flex xs12>
                        <PageForm :page="page"></PageForm>
                    </v-flex>
                </v-layout>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    import PageForm from './PageForm.vue';

    import messages from '../lang/PageUpdate';

    export default {
        i18n : {
            messages
        },
        components : {
            PageForm
        },
        data() {
            return {};
        },
        mounted() {
            this.fetchData(this.$route.params.id);
        },
        computed : {
            page() {
                return this.$store.getters['pageModule/page'](this.$route.params.id);
            }
        },
        beforeRouteUpdate(to, from, next) {
            this.fetchData(to.params.id);
            next();
        },
        methods : {
            fetchData(id) {
                this.$store.dispatch('pageModule/read', {
                    id : id
                });
            }
        }
    };
</script>
