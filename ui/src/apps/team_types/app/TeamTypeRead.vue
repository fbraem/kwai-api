<template>
    <Page>
        <template slot="title">{{ $t('types') }}
            <span v-if="teamtype">&nbsp;&bull;&nbsp;{{ teamtype.name }}</span>
        </template>
        <template slot="toolbar">
            <router-link v-if="teamtype && $team_type.isAllowed('update', teamtype)" class="uk-icon-button" :to="{ 'name' : 'team_types.update', params : { id : teamtype.id } }">
                <fa-icon name="edit" />
            </router-link>
        </template>
        <div slot="content" class="uk-container">
            <div v-if="notAllowed" class="uk-alert-danger" uk-alert>
                {{ $t('not_allowed') }}
            </div>
            <div v-if="notFound" class="uk-alert-danger" uk-alert>
                {{ $t('not_found') }}
            </div>
            <div v-if="$wait.is('teams.read')" class="uk-flex-center" uk-grid>
                <div class="uk-text-center">
                    <fa-icon name="spinner" scale="2" spin />
                </div>
            </div>
            <div v-if="teamtype" class="uk-child-width-1-1" uk-grid>
                <div>
                    <table class="uk-table uk-table-striped">
                        <tr>
                            <th>{{ $t('name') }}</th>
                            <td>{{ teamtype.name }}</td>
                        </tr>
                        <tr>
                            <th>{{ $t('form.start_age.label') }}</th>
                            <td>{{ teamtype.start_age }}</td>
                        </tr>
                        <tr>
                            <th>{{ $t('form.end_age.label') }}</th>
                            <td>{{ teamtype.end_age }}</td>
                        </tr>
                        <tr>
                            <th>{{ $t('form.remark.label') }}</th>
                            <td>{{ teamtype.remark }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </Page>
<!--
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
-->
</template>

<script>
    import messages from '../lang';

    import Page from './Page';

    import teamTypeStore from '../store';

    export default {
        components : {
            Page
        },
        i18n : messages,
        computed : {
            teamtype() {
                return this.$store.getters['teamTypeModule/type'](this.$route.params.id);
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
            },
            error() {
                return this.$store.getters['teamTypeModule/error'];
            },
            notAllowed() {
                return this.error && this.error.response.status == 401;
            },
            notFound() {
                return this.error && this.error.response.status == 404;
            }

        },
        beforeCreate() {
            if (!this.$store.state.teamTypeModule) {
                this.$store.registerModule('teamTypeModule', teamTypeStore);
            }
        },
        beforeRouteEnter(to, from, next) {
            next(async (vm) => {
                await vm.fetchData(to.params.id);
                next();
            });
        },
        methods : {
            fetchData(id) {
                this.$store.dispatch('teamTypeModule/read', { id : id })
                    .catch((error) => {
                        console.log(error);
                });
            }
        }
    };
</script>
