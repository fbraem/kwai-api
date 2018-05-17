<template>
    <v-container fluid>
        <v-layout row wrap>
            <v-flex xs12>
                <v-toolbar class="elevation-0">
                    <v-toolbar-title>{{ $t('seasons') }}</v-toolbar-title>
                    <v-spacer></v-spacer>
                </v-toolbar>
            </v-flex>
        </v-layout>
        <v-container>
            <router-view name="SeasonContent"></router-view>
        </v-container>
    </v-container>
</template>

<script>
    import messages from './lang/lang';
    import seasonStore from './store';

    export default {
        i18n : {
            messages
        },
        created() {
            if (!this.$store.state.seasonModule) {
                this.$store.registerModule('seasonModule', seasonStore);
            }
            this.$store.dispatch('setSubTitle', this.$t('seasons'));
        },
        methods : {
            selectSeason(id) {
                this.$router.push({ name : 'season.read', params : { id : id }});
            }
        }
    };
</script>
