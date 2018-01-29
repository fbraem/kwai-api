<template>
    <v-container>
        <v-layout>
            <v-flex xs12 lg10 offset-lg1>
                <v-layout row wrap>
                    <v-flex xs12>
                        <v-toolbar class="elevation-0">
                            <v-icon>add</v-icon>
                            <v-toolbar-title>{{ $t('create') }}</v-toolbar-title>
                            <v-spacer></v-spacer>
                            <v-btn icon dark color="primary" @click="$router.go(-1)"><v-icon>fa-times</v-icon></v-btn>
                        </v-toolbar>
                    </v-flex>
                    <v-flex xs12>
                        <PageForm :page="null" />
                    </v-flex>
                </v-layout>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    import PageForm from './PageForm.vue';

    import messages from '../lang/PageCreate.js';

    export default {
        i18n : {
            messages : messages
        },
        components : {
            PageForm
        }
    };
</script>
