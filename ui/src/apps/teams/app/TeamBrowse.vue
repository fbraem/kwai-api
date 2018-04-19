<template>
    <v-container fluid>
        <v-layout row wrap>
            <v-flex xs4>
                <v-card>
                    <v-card-title class="pb-0">
                        <div>
                            <h3 class="headline mb-0">{{ $t('teams') }}</h3>
                        </div>
                    </v-card-title>
                    <v-card-text v-if="items">
                        <v-list>
                            <v-list-group v-for="(item, seasonName) in items" :key="seasonName" v-model="item.open">
                                <v-list-tile slot="item" @click="">
                                    <v-list-tile-content>
                                        <v-list-tile-title>{{ seasonName }}</v-list-tile-title>
                                    </v-list-tile-content>
                                    <v-list-tile-action>
                                      <v-icon>keyboard_arrow_down</v-icon>
                                    </v-list-tile-action>
                                </v-list-tile>
                                <TeamListItem v-for="team in item.teams" :key="team.id" :team="team" />
                            </v-list-group>
                        </v-list>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn v-if="$isAllowed('create')" icon :to="{ name : 'team.create' }" fab small>
                            <v-icon>fa-plus</v-icon>
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-flex>
            <v-flex xs8>
                <router-view name="TeamContent"></router-view>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    import messages from '../lang/lang';

    import TeamListItem from './TeamListItem';

    export default {
        components : {
            TeamListItem
        },
        i18n : {
            messages
        },
        data() {
            return {
            };
        },
        computed : {
            items() {
                var items = {};
                var teams = this.$store.getters['teamModule/teams'];
                if (teams) {
                    teams.forEach((team) => {
                        var seasonName = team.season ? team.season.name : 'No Season';
                        var season = items[seasonName];
                        if ( !season) {
                            season = {
                                open : false,
                                teams : []
                            };
                            items[seasonName] = season;
                        }
                        season.teams.push(team);
                    });
                }
                return items;
            }
        }
    };
</script>
