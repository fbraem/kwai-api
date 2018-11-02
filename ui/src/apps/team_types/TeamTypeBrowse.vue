<template>
    <div>
        <PageHeader>
            <div class="uk-grid">
                <div class="uk-width-5-6">
                    <h1>{{ $t('types') }}</h1>
                </div>
                <div class="uk-width-1-6">
                    <div class="uk-flex uk-flex-right">
                        <div>
                            <router-link v-if="$team_type.isAllowed('create')" class="uk-icon-button" :to="{ name : 'team_types.create' }">
                                <fa-icon name="plus" />
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </PageHeader>
        <section class="uk-section uk-section-small uk-container uk-container-expand">
            <div v-if="$wait.is('teamtypes.browse')" class="uk-flex-center" uk-grid>
                <div class="uk-text-center">
                    <fa-icon name="spinner" scale="2" spin />
                </div>
            </div>
            <div v-else class="uk-child-width-1-1" uk-grid>
                <div v-if="noTypes" class="uk-alert uk-alert-warning">
                    {{ $t('no_types') }}
                </div>
                <div v-else>
                    <table class="uk-table uk-table-striped">
                        <tr>
                            <th>{{ $t('name') }}</th>
                            <th class="uk-table-shrink"></th>
                        </tr>
                        <tr v-for="type in types" :key="type.id">
                            <td>
                                <router-link :to="{ name: 'team_types.read', params: { id : type.id} }">{{ type.name }}</router-link>
                            </td>
                            <td>
                                <router-link v-if="$team_type.isAllowed('update', type)" class="uk-icon-button" style="margin-top:-10px" :to="{ name : 'team_types.update', params : { id : type.id } }">
                                    <fa-icon name="edit" />
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
    import 'vue-awesome/icons/spinner';
    import 'vue-awesome/icons/plus';

    import messages from './lang';

    import PageHeader from '@/site/components/PageHeader';

    import teamTypeStore from '@/stores/team_types';

    export default {
        components : {
            PageHeader
        },
        i18n : messages,
        computed : {
            types() {
                return this.$store.getters['teamTypeModule/types'];
            },
            noTypes() {
                return this.types && this.types.length == 0;
            }
        },
        beforeCreate() {
            if (!this.$store.state.teamTypeModule) {
                this.$store.registerModule('teamTypeModule', teamTypeStore);
            }
        },
        beforeRouteEnter(to, from, next) {
            next(async (vm) => {
                await vm.fetchData();
                next();
            });
        },
        methods : {
            fetchData() {
                this.$store.dispatch('teamTypeModule/browse');
            }
        }
    };
</script>
