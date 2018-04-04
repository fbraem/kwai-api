<template>
    <v-layout class="mt-3">
        <v-flex>
            <v-toolbar class="elevation-0">
                <v-icon>add</v-icon>
                <v-toolbar-title>{{ $t('type.create_title') }}</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn icon dark color="primary" @click="$router.go(-1)"><v-icon>cancel</v-icon></v-btn>
            </v-toolbar>
            <TeamTypeForm :type="null" />
        </v-flex>
    </v-layout>
</template>

<script>
    import messages from '../lang/lang';

    import TeamTypeForm from './TeamTypeForm.vue';
    export default {
        components : {
            TeamTypeForm
        },
        i18n : {
            messages
        }
    };
</script>
