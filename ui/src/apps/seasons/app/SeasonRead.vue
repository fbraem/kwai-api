<template>
    <v-container fluid>
        <v-layout row wrap>
            <v-flex v-if="season" xs12>
                <v-card>
                    <v-card-title>
                        <div>
                            {{ season.name }}
                        </div>
                    </v-card-title>
                    <v-card-text>
                        <v-text-field readonly name="start" :label="$t('start_date')" :value="season.formatted_start_date" />
                        <v-text-field readonly name="end" :label="$t('end_date')" :value="season.formatted_end_date" />
                        <v-text-field readonly multi-line name="remark" :label="$t('remark')" :value="season.remark" />
                        <div v-if="active">
                            <v-icon v-if="active">fa-check</v-icon>
                            <span style="vertical-align:bottom">&nbsp;&nbsp;{{ $t('active_message') }}</span>
                        </div>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn v-if="$isAllowed('update', season)" color="secondary" icon :to="{ name : 'season.update', params : { id : season.id }}" flat>
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
            season() {
                return this.$store.getters['seasonModule/season'](this.$route.params.id);
            },
            active() {
                var today = moment();
                return today.isBetween(this.season.start_date, this.season.end_date)
                    || today.isSame(this.season.start_date)
                    || today.isSame(this.season.end_date);
            }
        },
        beforeRouteUpdate(to, from, next) {
            this.fetchData(to.params.id);
            next();
        },
        mounted() {
            this.fetchData(this.$route.params.id);
        },
        watch : {
            '$route'(to) {
                this.fetchData(to.params.id);
            }
        },
        methods : {
            fetchData(id) {
                this.$store.dispatch('seasonModule/read', { id : id })
                    .catch((error) => {
                        console.log(error);
                });
            }
        }
    };
</script>
