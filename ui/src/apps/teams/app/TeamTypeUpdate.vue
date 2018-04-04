<template>
    <v-container fluid>
        <v-toolbar class="elevation-0">
            <v-icon>edit</v-icon>
            <v-toolbar-title>{{ $t('type.update_title') }}</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon dark color="primary" @click="$router.go(-1)"><v-icon>cancel</v-icon></v-btn>
        </v-toolbar>
        <TeamTypeForm :teamtype="teamtype" />
    </v-container>
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
        },
        data() {
            return {};
        },
        mounted() {
            this.fetchData(this.$route.params.id);
        },
        computed : {
            teamtype() {
                return this.$store.getters['teamModule/type'](this.$route.params.id);
            }
        },
        methods : {
            fetchData(id) {
                this.$store.dispatch('teamModule/readType', {
                    id : id
                });
            }
        }
    };
</script>
