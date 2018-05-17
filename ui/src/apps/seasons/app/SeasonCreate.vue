<template>
    <v-layout>
        <v-flex xs12 lg8 offset-lg2>
            <v-layout row wrap>
                <v-flex xs12>
                    <v-toolbar class="elevation-0">
                        <v-icon>add</v-icon>
                        <v-toolbar-title>{{ $t('create') }}</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-btn icon dark color="primary" @click="$router.go(-1)"><v-icon>cancel</v-icon></v-btn>
                    </v-toolbar>
                </v-flex>
                <v-flex xs12>
                    <SeasonForm :season="null" />
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
        }
    };
</script>
