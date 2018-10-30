<template>
    <div>
        <PageHeader>
            <div class="uk-grid">
                <div class="uk-width-5-6">
                    <h1>{{ $t('seasons') }}</h1>
                </div>
                <div class="uk-width-1-6">
                    <div class="uk-flex uk-flex-right">
                        <router-link v-if="$page.isAllowed('create')" class="uk-icon-button" :to="{ name : 'seasons.create' }">
                            <fa-icon name="plus" />
                        </router-link>
                    </div>
                </div>
            </div>
        </PageHeader>
        <section class="uk-section uk-section-small uk-container uk-container-expand">
            <div v-if="$wait.is('seasons.browse')" class="uk-flex-center" uk-grid>
                <div class="uk-text-center">
                    <fa-icon name="spinner" scale="2" spin />
                </div>
            </div>
            <div v-else uk-grid>
                <div v-if="seasons && seasons.length == 0" class="uk-alert uk-alert-warning">
                    {{ $t('no_seasons') }}
                </div>
                <table v-else class="uk-table uk-table-striped">
                    <tr>
                        <th></th>
                        <th>{{ $t('name') }}</th>
                        <th>{{ $t('start_date') }}</th>
                        <th>{{ $t('end_date') }}</th>
                        <th></th>
                    </tr>
                    <tr v-for="season in seasons" :key="season.id">
                        <td>
                            <fa-icon name="check" v-if="season.active" />
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
                                <fa-icon name="edit" />
                            </router-link>
                        </td>
                    </tr>
                </table>
            </div>
        </section>
    </div>
</template>

<script>
    import 'vue-awesome/icons/spinner';
    import 'vue-awesome/icons/check';
    import 'vue-awesome/icons/edit';
    import 'vue-awesome/icons/plus';

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
