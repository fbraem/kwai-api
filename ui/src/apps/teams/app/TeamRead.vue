<template>
    <v-container fluid class="pt-0">
        <v-card v-if="team">
            <v-card-title class="pb-0" primary-title>
                <h4 class="headline mb-0">{{ $t('team.details') }}</h4>
            </v-card-title>
            <v-card-text>
                <v-container fluid grid-list-md>
                    <v-layout row wrap>
                        <v-flex xs12>
                            <v-text-field name="name" readonly :label="$t('team.form.name.label')" :value="team.name" />
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex xs12>
                            <v-text-field v-if="team.season" name="season" readonly :label="$t('team.form.season.label')" :value="team.season.name" />
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex xs12>
                            <v-text-field v-if="team.team_type" name="teamtype" readonly :label="$t('team.form.team_type.label')" :value="team.team_type.name" />
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex xs12>
                            <v-text-field readonly multi-line name="remark" :value="team.remark" label="Remark" />
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn v-if="$isAllowed('update', team)" color="secondary" icon :to="{ name : 'team.update', params : { id : team.id }}" flat>
                    <v-icon>fa-edit</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
        <v-card v-if="team" class="mt-4">
            <v-card-title class="pb-0" primary-title>
                <div class="mb-0">
                    <h4 class="headline">{{ $t('members') }}</h4>
                    <p class="mb-0" v-if="team.season">
                        Leeftijd is berekend op de einddatum van het seizoen {{ team.season.name }}: {{ seasonStart}} &ndash; {{ seasonEnd }}
                    </p>
                </div>
            </v-card-title>
            <v-card-actions>
                <v-btn v-if="$isAllowed('addMember', team)" icon fab small>
                    <v-icon>fa-plus</v-icon>
                </v-btn>
            </v-card-actions>
            <v-card-text>
                <v-list two-line>
                    <template v-for="(member, index) in members">
                        <MemberListItem :team="team" :member="member" />
                        <v-divider v-if="index + 1 < members.length"></v-divider>
                    </template>
                </v-list>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script>
    import messages from '../lang/lang';
    import moment from 'moment';

    import MemberListItem from './MemberListItem.vue';

    export default {
        props : [
            'id'
        ],
        components : {
            MemberListItem
        },
        i18n : {
            messages
        },
        computed : {
            team() {
                return this.$store.getters['teamModule/team'](this.id);
            },
            members() {
                return this.$store.getters['teamModule/members'](this.id);
            },
            seasonStart() {
                return moment(this.team.season.start_date, 'YYYY-MM-DD').format('L');
            },
            seasonEnd() {
                return moment(this.team.season.end_date, 'YYYY-MM-DD').format('L');
            }
        },
        created() {
            this.fetchData();
        },
        watch : {
            'team'(nv, ov) {
                if ( ov == null || nv.id != ov.id ) this.fetchMembers();
            }
        },
        methods : {
            fetchData() {
                this.$store.dispatch('teamModule/read', { id : this.id })
                    .catch((err) => {
                        console.log(err);
                    });
            },
            fetchMembers() {
                this.$store.dispatch('teamModule/members', { id : this.id })
                    .catch((err) => {
                        console.log(err);
                    });
            }
        }
    };
</script>
