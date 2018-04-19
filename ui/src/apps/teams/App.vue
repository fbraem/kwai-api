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
        <router-view name="ListContent"></router-view>
    </v-container>
</template>

<script>
    import messages from './lang/lang';

    export default {
        i18n : {
            messages
        },
        created() {
            this.$store.dispatch('setSubTitle', this.$t('teams'));
        }
    };
</script>
