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
                    <p class="mb-0" v-if="team.season" v-html="$t('age_remark', { season : team.season.name, start : seasonStart, end : seasonEnd})">
                    </p>
                </div>
            </v-card-title>
            <v-card-actions>
                <v-btn v-if="$isAllowed('attachMember', team)" icon fab small @click.native="showAddMemberDialog">
                    <v-icon>fa-plus</v-icon>
                </v-btn>
                <v-btn v-if="$isAllowed('detachMember', team) && selectedMembers.length > 0" icon fab small @click.native="areYouSure">
                    <v-icon class="far">fa-trash-alt</v-icon>
                </v-btn>
            </v-card-actions>
            <v-divider></v-divider>
            <v-card-text v-if="members">
                <MemberList v-model="selectedMembers" :team="team" :members="members" />
                <div v-if="members.length == 0">
                    {{ $t('no_members') }}
                </div>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-btn v-if="$isAllowed('attachMember', team)" icon fab small @click.native="showAddMemberDialog">
                    <v-icon>fa-plus</v-icon>
                </v-btn>
                <v-btn v-if="$isAllowed('detachMember', team) && selectedMembers.length > 0" icon fab small>
                    <v-icon class="far">fa-trash-alt</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
        <v-dialog v-if="team" v-model="addMemberDialog" scrollable max-width="450px">
            <v-card>
                <v-card-title primary-title>
                    <h4 class="headline mb-0">Add Members</h4>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <div v-if="team.team_type">
                        Members are automatically selected based on the team type.
                    </div>
                    <div v-else>
                        <v-container grid-list-xl>
                            <v-layout row wrap>
                                <v-flex xs4>
                                    <v-text-field
                                        label="Start Age"
                                        v-model="start_age" />
                                </v-flex>
                                <v-flex xs4>
                                    <v-text-field
                                        label="End Age"
                                        v-model="end_age" />
                                </v-flex>
                                <v-flex xs4>
                                    <v-select :items="genders" v-model="gender" label="Gender" class="input-group--focused"></v-select>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-spacer></v-spacer>
                                <v-btn @click.native="filterAvailableMembers">Filter</v-btn>
                                <v-spacer></v-spacer>
                            </v-layout>
                        </v-container>
                    </div>
                    <MemberList v-model="selectedAvailableMembers" :team="team" :members="availableMembers" />
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-btn flat @click.native="addMemberDialog = false">Annuleer</v-btn>
                    <v-btn flat @click.native="addMembers" :disabled="selectedAvailableMembers.length == 0">Add Members</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script>
    import Model from '@/js/model';
    import messages from '../lang/lang';
    import moment from 'moment';

    import MemberListItem from './MemberListItem.vue';
    import MemberList from './MemberList.vue';

    export default {
        props : [
            'id'
        ],
        data() {
            return {
                addMemberDialog : false,
                selectedMembers : [],
                selectedAvailableMembers : [],
                start_age : 0,
                end_age : 0,
                gender : 0,
                genders : [
                    { text : 'None', value : 0 },
                    { text : 'Male', value : 1 },
                    { text : 'Female', value : 2 }
                ]
            }
        },
        components : {
            MemberListItem,
            MemberList
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
            },
            availableMembers() {
                return this.$store.getters['teamModule/availableMembers'];
            }
        },
        created() {
            this.fetchData();
        },
        watch : {
            '$route'() {
                this.fetchData();
            }
        },
        methods : {
            fetchData() {
                this.$store.dispatch('teamModule/read', { id : this.id })
                    .then(() => {
                        this.fetchMembers();
                    })
                    .catch((err) => {
                        console.log(err);
                    });
            },
            fetchMembers() {
                this.$store.dispatch('teamModule/members', { id : this.id })
                    .catch((err) => {
                        console.log(err);
                    });
            },
            showAddMemberDialog() {
                if (this.team.team_type) {
                    this.$store.dispatch('teamModule/availableMembers', { id : this.id })
                        .catch((err) => {
                            console.log(err);
                        });
                }
                this.addMemberDialog = true;
            },
            filterAvailableMembers() {
                this.$store.dispatch('teamModule/availableMembers', {
                    id : this.id,
                    filter : {
                        start_age : this.start_age,
                        end_age : this.end_age,
                        gender : this.gender
                    }
                });
            },
            addMembers() {
                var members = [];
                this.selectedAvailableMembers.forEach((id) => {
                    members.push(new Model('members', id));
                });
                this.$store.dispatch('teamModule/addMembers', {
                    id : this.id,
                    members : members
                });
                this.addMemberDialog = true;
            },
            areYouSure() {
                var members = [];
                this.selectedMembers.forEach((id) => {
                    members.push(new Model('members', id));
                });
                this.$store.dispatch('teamModule/deleteMembers', {
                    id : this.id,
                    members : members
                });
                this.selectedMembers = [];
            }
        }
    };
</script>
