<template>
    <v-layout>
        <v-flex xs12 lg8 offset-lg2>
            <v-layout row wrap>
                <v-flex xs12>
                    <v-toolbar class="elevation-0">
                        <v-icon>edit</v-icon>
                        <v-toolbar-title>{{ $t('update') }}</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-btn icon dark color="primary" @click="$router.go(-1)"><v-icon>cancel</v-icon></v-btn>
                    </v-toolbar>
                </v-flex>
                <v-flex xs12>
                    <SeasonForm :season="season" />
                </v-flex>
            </v-layout>
        </v-flex>
    </v-layout>
</template>

<script>
    import SeasonForm from './SeasonForm.vue';
    import messages from '../lang/lang';

    export default {
        components : {
            SeasonForm
        },
        i18n : {
            messages
        },
        data() {
            return {};
        },
        mounted() {
            this.fetchData(this.$route.params.id);
        },
        computed : {
            season() {
                return this.$store.getters['seasonModule/season'](this.$route.params.id);
            }
        },
        methods : {
            fetchData(id) {
                this.$store.dispatch('seasonModule/read', {
                    id : id
                });
            }
        }
    };
</script>
