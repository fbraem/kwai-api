<template>
    <v-container fluid>
        <v-layout row wrap>
            <v-flex xs12>
                <v-toolbar class="elevation-0">
                    <v-toolbar-title>{{ $t('teams') }}</v-toolbar-title>
                    <v-spacer></v-spacer>
                </v-toolbar>
            </v-flex>
        </v-layout>
        <router-view name="TeamContent"></router-view>
    </v-container>
</template>

<script>
    import messages from './lang/lang';
    import teamStore from './store';

    export default {
        i18n : {
            messages
        },
        created() {
            if (!this.$store.state.teamModule) {
                this.$store.registerModule('teamModule', teamStore);
            }
            this.$store.dispatch('setSubTitle', this.$t('teams'));
        }
    };
</script>
