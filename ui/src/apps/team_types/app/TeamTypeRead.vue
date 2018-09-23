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
                            <th>{{ $t('form.gender.label') }}</th>
                            <td>{{ gender }}</td>
                        </tr>
                        <tr>
                            <th>{{ $t('active') }}</th>
                            <td>
                                <fa-icon v-if="teamtype.active" name="check" />
                                <fa-icon v-else name="times" class="uk-text-danger" />
                            </td>
                        </tr>
                        <tr>
                            <th>{{ $t('competition_label') }}<br /></th>
                            <td>
                                <fa-icon v-if="teamtype.competition" name="check" />
                                <fa-icon v-else name="times" class="uk-text-danger" />
                            </td>
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
</template>

<script>
    import 'vue-awesome/icons/check';
    import 'vue-awesome/icons/times';

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
