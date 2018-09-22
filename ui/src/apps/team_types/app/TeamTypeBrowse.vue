<template>
    <Page>
        <template slot="title">{{ $t('types') }}</template>
        <div slot="content" class="uk-container">
            <div v-if="$wait.is('teamtypes.browse')" class="uk-flex-center" uk-grid>
                <div class="uk-text-center">
                    <fa-icon name="spinner" scale="2" spin />
                </div>
            </div>
            <div v-else uk-grid>
                <div v-if="noTypes">
                    {{ $t('no_types') }}
                </div>
                <table v-else class="uk-table uk-table-striped">
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
<!--
            <v-card-text v-if="this.types">
                <v-list v-if="this.types.length > 0" two-line>
                    <TeamTypeListItem v-for="type in types" :key="type.id" :type="type" />
                </v-list>
                <div v-else>
                    {{ $t('no_types') }}.
                </div>
            </v-card-text>
        </v-card>
        <v-btn v-if="$isAllowed('create')" icon :to="{ name : 'teamtype.create' }" fab small>
            <v-icon>fa-plus</v-icon>
        </v-btn>
-->
    </Page>
</template>

<script>
    import 'vue-awesome/icons/spinner';

    import messages from '../lang';

    import Page from './Page';
    import teamTypeStore from '../store';

    export default {
        components : {
            Page
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
