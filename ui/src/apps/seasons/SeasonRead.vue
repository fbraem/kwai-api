<template>
    <Page>
        <template slot="title">
            {{ $t('seasons') }} <span v-if="season">&nbsp;&bull;&nbsp;{{ season.name }}</span>
        </template>
        <template slot="toolbar">
            <router-link v-if="season && $season.isAllowed('update', season)" class="uk-icon-button" :to="{ 'name' : 'seasons.update', params : { id : season.id } }">
                <fa-icon name="edit" />
            </router-link>
        </template>
        <div slot="content" class="uk-container">
            <div v-if="loading" class="uk-flex-center" uk-grid>
                <div class="uk-text-center">
                    <fa-icon name="spinner" scale="2" spin />
                </div>
            </div>
            <div v-if="season" uk-grid>
                <table class="uk-table">
                    <tr>
                        <th>{{ $t('name') }}</th>
                        <td>{{ season.name }}</td>
                    </tr>
                    <tr>
                        <th>{{ $t('start_date') }}</th>
                        <td>{{ season.formatted_start_date }}</td>
                    </tr>
                    <tr>
                        <th>{{ $t('end_date') }}</th>
                        <td>{{ season.formatted_end_date }}</td>
                    </tr>
                    <tr>
                        <th>{{ $t('remark') }}</th>
                        <td>{{ season.remark }}</td>
                    </tr>
                </table>
                <div v-if="season.active">
                    <fa-icon name="check" />
                    <span style="vertical-align:bottom">&nbsp;&nbsp;{{ $t('active_message') }}</span>
                </div>
            </div>
        </div>
    </Page>
</template>

<script>
    import 'vue-awesome/icons/check';
    import 'vue-awesome/icons/edit';

    import messages from './lang';

    import Page from './Page';

    import seasonStore from '@/stores/seasons';

    export default {
        components : {
            Page
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
            }
        }
    };
</script>
