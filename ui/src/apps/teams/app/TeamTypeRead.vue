<template>
    <v-container fluid>
        <v-layout row wrap>
            <v-flex v-if="teamtype" xs12>
                <v-card>
                    <v-card-title>
                        <div class="headline mb-0">
                            {{ $t('type.details') }} : {{ teamtype.name }}
                        </div>
                    </v-card-title>
                    <v-card-text>
                        <v-container fluid grid-list-md>
                            <v-layout row wrap>
                                <v-flex xs12 sm6>
                                    <v-text-field name="start_age" readonly :label="$t('type.form.min_age.label')" :hint="$t('type.form.min_age.hint')" persistent-hint :value="teamtype.start_age" />
                                </v-flex>
                                <v-spacer></v-spacer>
                                <v-flex xs12 sm6>
                                    <v-text-field name="end_age" :label="$t('type.form.max_age.label')" :hint="$t('type.form.max_age.hint')" persistent-hint :value="teamtype.end_age" />
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex xs12>
                                    <v-text-field name="gender" readonly :label="$t('type.form.gender.label')" :value="gender" :hint="$t('type.form.gender.hint')" persistent-hint />
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex xs12>
                                    <v-text-field readonly multi-line name="remark" :value="teamtype.remark" :label="$t('type.form.remark.label')" />
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex xs6>
                                    <div v-if="teamtype.active">
                                        <v-icon>fa-check</v-icon>
                                        <span style="vertical-align:bottom">&nbsp;&nbsp; {{ $t('active') }}</span>
                                    </div>
                                    <div v-else>
                                        {{ $t('not_active') }}
                                    </div>
                                </v-flex>
                                <v-flex xs6>
                                    <div v-if="teamtype.competition">
                                        <v-icon>fa-check</v-icon>
                                        <span style="vertical-align:bottom">&nbsp;&nbsp;{{ $t('used_competition') }}</span>
                                    </div>
                                    <div v-else>
                                        {{ $t('no_competition') }}
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
            },
            gender() {
                var gender = this.teamtype.gender;
                if ( gender == 0 ) {
                    return this.$t('no_restriction');
                }
                else if ( gender == 1 )  {
                    return this.$t('male');
                } else {
                    return this.$t('female');
                }
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
