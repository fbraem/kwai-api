<template>
    <div>
        <PageHeader>
            <div class="uk-grid">
                <div class="uk-width-1-1@s uk-width-5-6@m">
                    <h1 class="uk-h1">{{ $t('seasons') }}</h1>
                    <h3 v-if="season" class="uk-h3 uk-margin-remove">{{ season.name }}</h3>
                </div>
                <div class="uk-width-1-1@s uk-width-1-6@m">
                    <div class="uk-flex uk-flex-right">
                        <div>
                            <router-link class="uk-icon-button" :to="{ 'name' : 'seasons.browse' }">
                                <fa-icon name="list" />
                            </router-link>
                        </div>
                        <div class="uk-margin-small-left">
                            <router-link v-if="season && $season.isAllowed('update', season)" class="uk-icon-button" :to="{ 'name' : 'seasons.update', params : { id : season.id } }">
                                <fa-icon name="edit" />
                            </router-link>
                        </div>
                        <div v-if="season && $season.isAllowed('remove', season)" class="uk-margin-small-left">
                            <a uk-toggle="target: #delete-season" class="uk-icon-button">
                                <fa-icon name="trash" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </PageHeader>
        <section class="uk-section uk-section-small uk-container uk-container-expand">
            <AreYouSure id="delete-season" :yes="$t('delete')" :no="$t('cancel')" @sure="deleteSeason">
                {{ $t('are_you_sure') }}
            </AreYouSure>
            <div v-if="$wait.is('seasons.read')" class="uk-flex-center" uk-grid>
                <div class="uk-text-center">
                    <fa-icon name="spinner" scale="2" spin />
                </div>
            </div>
            <div v-else-if="season" class="uk-grid uk-grid-divider" uk-grid>
                <div class="uk-width-1-1@s uk-width-1-2@m">
                    <div>
                        <h3 class="uk-heading-line"><span>{{ $t('season') }}</span></h3>
                        <table class="uk-table uk-table-divider">
                            <tr>
                                <th class="uk-text-top">{{ $t('name') }}</th>
                                <td class="uk-table-expand">{{ season.name }}</td>
                            </tr>
                            <tr>
                                <th class="uk-text-top">{{ $t('start_date') }}</th>
                                <td>{{ season.formatted_start_date }}</td>
                            </tr>
                            <tr>
                                <th class="uk-text-top">{{ $t('end_date') }}</th>
                                <td>{{ season.formatted_end_date }}</td>
                            </tr>
                            <tr>
                                <th class="uk-text-top">{{ $t('remark') }}</th>
                                <td>{{ season.remark }}</td>
                            </tr>
                        </table>
                        <div v-if="season.active">
                            <fa-icon name="check" />
                            <span style="vertical-align:bottom">&nbsp;&nbsp;{{ $t('active_message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="uk-width-1-1@s uk-width-1-2@m" v-if="season">
                    <div>
                        <h3 class="uk-heading-line"><span>{{ $t('teams') }}</span></h3>
                        <table v-if="season.teams" class="uk-table uk-table-divider">
                            <tr v-for="team in season.teams">
                                <td>
                                    <router-link :to="{ 'name' : 'teams.read', params : { id : team.id } }">
                                        {{ team.name }}
                                    </router-link>
                                </td>
                            </tr>
                        </table>
                        <div v-else class="uk-alert uk-alert-warning">
                            {{ $t('no_teams') }}
                        </div>
                    </div>
                    <div class="uk-flex uk-flex-right">
                        <div>
                            <router-link class="uk-icon-button" :to="{ 'name' : 'teams.browse' }">
                                <fa-icon name="list" />
                            </router-link>
                        </div>
                        <div class="uk-margin-small-left">
                            <router-link v-if="$team.isAllowed('create')" class="uk-icon-button" :to="{ 'name' : 'teams.create', params : { season : season.id } }">
                                <fa-icon name="plus" />
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="uk-width-1-1 uk-alert uk-alert-danger">
                {{ $t('season_not_found') }}
            </div>
        </section>
    </div>
</template>

<script>
    import 'vue-awesome/icons/check';
    import 'vue-awesome/icons/list';
    import 'vue-awesome/icons/edit';
    import 'vue-awesome/icons/plus';

    import messages from './lang';

    import PageHeader from '@/site/components/PageHeader.vue';
    import AreYouSure from '@/components/AreYouSure.vue';

    import seasonStore from '@/stores/seasons';

    export default {
        components : {
            PageHeader,
            AreYouSure
        },
        i18n : messages,
        computed : {
            loading() {
                return this.$store.getters['seasonModule/loading'];
            },
            season() {
                return this.$store.getters['seasonModule/season'](this.$route.params.id);
            }
        },
        beforeRouteUpdate(to, from, next) {
            this.fetchData(to.params.id);
            next();
        },
        beforeCreate() {
            if (!this.$store.state.seasonModule) {
                this.$store.registerModule('seasonModule', seasonStore);
            }
        },
        created() {
            this.fetchData(this.$route.params.id);
        },
        methods : {
            fetchData(id) {
                this.$store.dispatch('seasonModule/read', { id : id })
                    .catch((error) => {
                        console.log(error);
                });
            },
            deleteSeason() {
                console.log('delete');
            }
        }
    };
</script>
