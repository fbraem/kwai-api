<template>
    <Page>
        <template slot="title">
            {{ $t('seasons') }}
        </template>
        <div slot="content" class="uk-container">
            <div v-if="$wait.is('seasons.browse')" class="uk-flex-center" uk-grid>
                <div class="uk-text-center">
                    <fa-icon name="spinner" scale="2" spin />
                </div>
            </div>
            <div v-else uk-grid>
                <div v-if="seasons && seasons.length == 0">
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
        </div>
    </Page>
</template>

<script>
    import 'vue-awesome/icons/spinner';
    import 'vue-awesome/icons/check';
    import 'vue-awesome/icons/edit';

    import messages from '../lang';

    import Page from './Page';
    import seasonStore from '@/stores/seasons';

    export default {
        components : {
            Page
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
