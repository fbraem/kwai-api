<template>
    <Page>
        <template slot="title">
            {{ $t('teams') }}
        </template>
        <div slot="content" class="uk-container">
            <div v-if="$wait.is('teams.browse')" class="uk-flex-center" uk-grid>
                <div class="uk-text-center">
                    <fa-icon name="spinner" scale="2" spin />
                </div>
            </div>
            <div v-else uk-grid>
                <div v-if="teams && teams.length == 0">
                    {{ $t('no_teams') }}
                </div>
                <table v-else class="uk-table uk-table-divider">
                    <tr>
                        <th>{{ $t('name') }}</th>
                        <th class="uk-table-shrink"></th>
                    </tr>
                    <tr v-for="team in teams" :key="team.id">
                        <td>
                            <router-link :to="{ name: 'teams.read', params: { id : team.id} }">{{ team.name }}</router-link>
                        </td>
                        <td>
                            <router-link v-if="$team.isAllowed('update', team)" class="uk-icon-button" style="margin-top:-10px" :to="{ name : 'teams.update', params : { id : team.id } }">
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

    import messages from './lang';

    import Page from './Page';
    import teamStore from '@/stores/teams';

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
            teams() {
                return this.$store.getters['teamModule/teams'];
            }
        },
        beforeCreate() {
            if (!this.$store.state.teamModule) {
                this.$store.registerModule('teamModule', teamStore);
            }
        },
        mounted() {
            this.fetchData();
        },
        methods : {
            fetchData() {
                this.$store.dispatch('teamModule/browse');
            }
        }
    };
</script>
