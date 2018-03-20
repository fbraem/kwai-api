<template>
    <v-container fluid>
        <v-card>
            <v-card-title>
                <div>
                    <h3 class="headline mb-0">{{ $t('teams') }}</h3>
                </div>
            </v-card-title>
            <v-card-text v-if="this.teams">
                <v-list v-if="this.teams.length > 0" two-line>
                    <TeamListItem v-for="team in teams" :key="team.id" :team="team" />
                </v-list>
                <div v-else>
                    {{ $t('no_teams') }}.
                </div>
            </v-card-text>
        </v-card>
        <v-btn v-if="$isAllowed('create')" icon :to="{ name : 'team.create' }" fab small>
            <v-icon>fa-plus</v-icon>
        </v-btn>
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
        mounted() {
            this.$store.dispatch('teamModule/browse');
        },
        computed : {
            teams() {
                return this.$store.getters['teamModule/teams'];
            }
        }
    };
</script>
