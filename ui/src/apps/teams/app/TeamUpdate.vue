<template>
    <v-container fluid>
        <v-toolbar class="elevation-0">
            <v-icon>edit</v-icon>
            <v-toolbar-title>{{ $t('team_update') }}</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon dark color="primary" @click="$router.go(-1)"><v-icon>cancel</v-icon></v-btn>
        </v-toolbar>
        <TeamForm :team="team" />
    </v-container>
</template>

<script>
    import messages from '../lang/lang';

    import TeamForm from './TeamForm.vue';
    export default {
        components : {
            TeamForm
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
            team() {
                return this.$store.getters['teamModule/team'](this.$route.params.id);
            }
        },
        methods : {
            fetchData(id) {
                this.$store.dispatch('teamModule/read', {
                    id : id
                });
            }
        }
    };
</script>
