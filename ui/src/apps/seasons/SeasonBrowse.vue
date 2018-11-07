<template>
    <div>
        <PageHeader>
            <div class="uk-grid">
                <div class="uk-width-5-6">
                    <h1>{{ $t('seasons') }}</h1>
                </div>
                <div class="uk-width-1-6">
                    <div class="uk-flex uk-flex-right">
                        <router-link v-if="$season.isAllowed('create')" class="uk-icon-button" :to="{ name : 'seasons.create' }">
                            <i class="fas fa-plus"></i>
                        </router-link>
                    </div>
                </div>
            </div>
        </PageHeader>
        <section class="uk-section uk-section-small uk-container uk-container-expand">
            <div v-if="$wait.is('seasons.browse')" class="uk-flex-center" uk-grid>
                <div class="uk-text-center">
                    <i class="fas fa-spinner fa-2x fa-spin"></i>
                </div>
            </div>
            <div v-else class="uk-child-width-1-1" uk-grid>
                <div v-if="seasons && seasons.length == 0" class="uk-alert uk-alert-warning">
                    {{ $t('no_seasons') }}
                </div>
                <div v-else>
                    <table class="uk-table uk-table-striped">
                        <tr>
                            <th></th>
                            <th>{{ $t('name') }}</th>
                            <th>{{ $t('start_date') }}</th>
                            <th>{{ $t('end_date') }}</th>
                            <th></th>
                        </tr>
                        <tr v-for="season in seasons" :key="season.id">
                            <td>
                                <i class="fas fa-check" v-if="season.active"></i>
                            </td>
                            <td>
                                <router-link :to="{ name: 'seasons.read', params: { id : season.id} }">{{ season.name }}</router-link>
                            </td>
                            <td>
                                {{ season.formatted_start_date }}
                            </td>
                            <td>
                                {{ season.formatted_end_date }}
                            </td>
                            <td>
                                <router-link v-if="$season.isAllowed('update', season)" class="uk-icon-button" style="margin-top:-10px" :to="{ name : 'seasons.update', params : { id : season.id } }">
                                    <i class="fas fa-edit"></i>
                                </router-link>
                            </td>
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
    import seasonStore from '@/stores/seasons';

    export default {
        components : {
            PageHeader
        },
        i18n : messages,
        data() {
            return {
            };
        },
        computed : {
            seasons() {
                return this.$store.getters['seasonModule/seasons'];
            }
        },
        beforeCreate() {
            if (!this.$store.state.seasonModule) {
                this.$store.registerModule('seasonModule', seasonStore);
            }
        },
        created() {
            this.fetchData();
        },
        methods : {
            fetchData() {
                this.$store.dispatch('seasonModule/browse');
            }
        }
    };
</script>
