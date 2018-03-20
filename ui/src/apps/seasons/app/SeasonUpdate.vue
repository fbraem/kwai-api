<template>
    <v-container fluid>
        <SeasonForm :season="season" />
    </v-container>
</template>

<script>
    import SeasonForm from './SeasonForm.vue';
    export default {
        components : {
            SeasonForm
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
