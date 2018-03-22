<template>
    <v-container fluid>
        <v-card v-if="team" class="mb-5">
            <v-card-title primary-title>
                <h4 class="headline mb-0">{{ $t('team.details') }}</h4>
            </v-card-title>
            <v-card-text>
                <v-container fluid grid-list-md>
                    <v-layout row wrap>
                        <v-flex xs12>
                            <v-text-field name="name" readonly :label="$t('team.form.name.label')" :value="team.name" />
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex xs12>
                            <v-text-field v-if="team.season" name="season" readonly :label="$t('team.form.season.label')" :value="team.season.name" />
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex xs12>
                            <v-text-field v-if="team.team_type" name="teamtype" readonly :label="$t('team.form.team_type.label')" :value="team.team_type.name" />
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex xs12>
                            <v-text-field readonly multi-line name="remark" :value="team.remark" label="Remark" />
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn v-if="$isAllowed('update', team)" color="secondary" icon :to="{ name : 'team.update', params : { id : team.id }}" flat>
                    <v-icon>fa-edit</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-container>
</template>

<script>
    import messages from '../lang/lang';
    import moment from 'moment';

    export default {
        i18n : {
            messages
        },
        computed : {
            team() {
                return this.$store.getters['teamModule/team'](this.$route.params.id);
            }
        },
        beforeRouteUpdate(to, from, next) {
            console.log('beforeRouteUpdate');
            this.fetchData(to.params.id);
            next();
        },
        mounted() {
            console.log('mounted');
            this.fetchData(this.$route.params.id);
        },
        watch : {
            '$route'(to) {
                console.log('watch');
                this.fetchData(to.params.id);
            }
        },
        methods : {
            fetchData(id) {
                this.$store.dispatch('teamModule/read', { id : id })
                    .catch((error) => {
                        console.log(error);
                });
            }
        }
    };
</script>
