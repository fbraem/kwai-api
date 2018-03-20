<template>
    <v-container fluid>
        <v-layout row wrap>
            <v-flex v-if="teamtype" xs12>
                <v-card>
                    <v-card-title>
                        <div class="headline mb-0">
                            {{ teamtype.name }}
                        </div>
                    </v-card-title>
                    <v-card-text>
                        <v-container fluid grid-list-md>
                            <v-layout row wrap>
                                <v-flex xs12 sm6>
                                    <v-text-field name="start_age" readonly label="Min. Age" :value="teamtype.start_age" />
                                </v-flex>
                                <v-spacer></v-spacer>
                                <v-flex xs12 sm6>
                                    <v-text-field name="end_age" label="Max. Age" :value="teamtype.end_age" />
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex xs12>
                                    <v-text-field readonly multi-line name="remark" :value="teamtype.remark" label="Remark" />
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex xs6>
                                    <div v-if="teamtype.active">
                                        <v-icon>fa-check</v-icon>
                                        <span style="vertical-align:bottom">&nbsp;&nbsp;Active</span>
                                    </div>
                                    <div v-else>
                                        This type is not active
                                    </div>
                                </v-flex>
                                <v-flex xs6>
                                    <div v-if="teamtype.competition">
                                        <v-icon>fa-check</v-icon>
                                        <span style="vertical-align:bottom">&nbsp;&nbsp;This teamtype is used for competition</span>
                                    </div>
                                    <div v-else>
                                        This teamtype is not used for competition
                                    </div>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn v-if="$isAllowed('update', teamtype)" color="secondary" icon :to="{ name : 'teamtype.update', params : { id : teamtype.id }}" flat>
                            <v-icon>fa-edit</v-icon>
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-flex>
        </v-layout>
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
            teamtype() {
                return this.$store.getters['teamModule/type'](this.$route.params.id);
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
                this.$store.dispatch('teamModule/readType', { id : id })
                    .catch((error) => {
                        console.log(error);
                });
            }
        }
    };
</script>
