<template>
    <div>
        <PageHeader>
            <div class="uk-grid">
                <div class="uk-width-5-6">
                    <h1>{{ $t('types') }}</h1>
                    <h3 v-if="teamtype" class="uk-h3 uk-margin-remove">{{ teamtype.name }}</h3>
                </div>
                <div class="uk-width-1-6">
                    <div class="uk-flex uk-flex-right">
                        <div>
                            <router-link class="uk-icon-button uk-link-reset" :to="{ 'name' : 'team_types.browse' }">
                                <i class="fas fa-list"></i>
                            </router-link>
                        </div>
                        <div class="uk-margin-small-left">
                            <router-link v-if="$team_type.isAllowed('update',teamtype)" class="uk-icon-button uk-link-reset" :to="{ name : 'team_types.update' }">
                                <i class="fas fa-edit"></i>
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </PageHeader>
        <section class="uk-section uk-section-small uk-container uk-container-expand">
            <div v-if="$wait.is('teamtypes.read')" class="uk-flex-center" uk-grid>
                <div class="uk-text-center">
                    <i class ="fas fa-spinner fa-2x fa-spin"></i>
                </div>
            </div>
            <div v-if="notAllowed" class="uk-alert-danger" uk-alert>
                {{ $t('not_allowed') }}
            </div>
            <div v-if="notFound" class="uk-alert-danger" uk-alert>
                {{ $t('not_found') }}
            </div>
            <div v-if="$wait.is('teams.read')" class="uk-flex-center" uk-grid>
                <div class="uk-text-center">
                    <i class ="fas fa-spinner fa-2x fa-spin"></i>
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
                                <i class="fas fa-check" v-if="teamtype.active"></i>
                                <i class="fas fa-times uk-text-danger" v-else name="times"></i>
                            </td>
                        </tr>
                        <tr>
                            <th>{{ $t('competition_label') }}<br /></th>
                            <td>
                                <i class="fas fa-check" v-if="teamtype.competition"></i>
                                <i class="fas fa-times uk-text-danger" v-else></i>
                            </td>
                        </tr>
                        <tr>
                            <th>{{ $t('form.remark.label') }}</th>
                            <td>{{ teamtype.remark }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
    import messages from './lang';

    import PageHeader from '@/site/components/PageHeader';

    import teamTypeStore from '@/stores/team_types';

    export default {
        components : {
            PageHeader
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
