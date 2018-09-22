<template>
    <Page>
        <template slot="title">
            {{ $t('teams') }}<span v-if="team">&nbsp;&bull;&nbsp;{{ team.name }}</span>
        </template>
        <template slot="toolbar">
            <router-link v-if="team && $team.isAllowed('update', team)" class="uk-icon-button" :to="{ 'name' : 'teams.update', params : { id : team.id } }">
                <fa-icon name="edit" />
            </router-link>
        </template>
        <div slot="content" class="uk-container">
            <AreYouSure id="delete-member" :yes="$t('delete')" :no="$t('cancel')" @sure="deleteMembers">
                <template slot="title">{{ $t('delete') }}</template>
                {{ $t('sure_to_delete') }}
            </AreYouSure>
            <div id="addMemberDialog" uk-modal ref="addMemberDialog">
                <div v-if="team" class="uk-modal-dialog uk-modal-body">
                    <div class="uk-child-width-1-1" uk-grid>
                        <div>
                            <h2 class="uk-modal-title">{{ $t('add_members') }}</h2>
                            <p class="uk-text-meta" v-if="team.team_type">
                                {{ $t('add_members_info') }}
                            </p>
                        </div>
                        <div>
                            <form class="uk-form uk-child-width-1-4 uk-flex-middle" v-if="! team.team_type" uk-grid>
                                <div>
                                    <uikit-input-text v-model="start_age" id="start_age">
                                        {{ $t('type.form.min_age.label') }}:
                                    </uikit-input-text>
                                </div>
                                <div>
                                    <uikit-input-text v-model="end_age" id="end_age">
                                        {{ $t('type.form.max_age.label') }}:
                                    </uikit-input-text>
                                </div>
                                <div>
                                    <uikit-select v-model="gender" :items="genders">
                                        {{ $t('type.form.gender.label') }}:
                                    </uikit-select>
                                </div>
                                <div>
                                    <label class="uk-form-label">&nbsp;</label>
                                    <button class="uk-button uk-button-primary" @click="filterAvailableMembers">
                                        Filter
                                    </button>
                                </div>
                            </form>
                            <p class="uk-text-meta" v-if="team.season" v-html="$t('age_remark', { season : team.season.name, start : team.season.formatted_start_date, end : team.season.formatted_end_date})"></p>
                            <hr />
                        </div>
                        <div v-if="$wait.is('teams.availableMembers')" class="uk-flex-center" uk-grid>
                            <div class="uk-text-center">
                                <fa-icon name="spinner" scale="2" spin />
                            </div>
                        </div>
                        <div class="uk-overflow-auto uk-height-medium" v-if="availableMembers && availableMembers.length > 0">
                            <table class="uk-table uk-table-small uk-table-middle uk-table-divider">
                                <tr v-for="member in availableMembers" :key="member.id">
                                    <td>
                                        <input class="uk-checkbox" type="checkbox" v-model="selectedAvailableMembers" :value="member.id">
                                    </td>
                                    <td>
                                        <strong>{{ member.person.name }}</strong><br />
                                        {{ member.person.formatted_birthdate }} ({{ memberAge(member) }})
                                    </td>
                                    <td>
                                        {{ member.license }}<br />
                                        <fa-icon v-if="member.person.gender == 1" name="male" />
                                        <fa-icon v-if="member.person.gender == 2" name="female" />
                                        <fa-icon v-if="member.person.gender == 0" name="question" />
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div v-else-if="! $wait.is('teams.availableMembers') ">
                            <p class="uk-text-meta">
                                Use filter to get a list of members that can be added this team.
                            </p>
                        </div>
                        <div>
                            <hr />
                            <button class="uk-button uk-button-default" @click="hideAddMemberDialog">
                                <fa-icon name="ban" />&nbsp; {{ $t('cancel') }}
                            </button>
                            <button class="uk-button uk-button-primary" :disabled="selectedAvailableMembers.length == 0" @click="addMembers">
                                <fa-icon name="plus" />&nbsp; {{ $t('add') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="notAllowed" class="uk-alert-danger" uk-alert>
                {{ $t('not_allowed') }}
            </div>
            <div v-if="notFound" class="uk-alert-danger" uk-alert>
                {{ $t('not_found') }}
            </div>
            <div v-if="$wait.is('teams.read')" class="uk-flex-center" uk-grid>
                <div class="uk-text-center">
                    <fa-icon name="spinner" scale="2" spin />
                </div>
            </div>
            <div v-if="team" class="uk-child-width-1-1" uk-grid>
                <div>
                    <table class="uk-table uk-table-striped">
                        <tr>
                            <th>{{ $t('name') }}</th>
                            <td>{{ team.name }}</td>
                        </tr>
                        <tr>
                            <th>{{ $t('team.form.season.label') }}</th>
                            <td v-if="team.season">{{ team.season.name }}</td>
                            <td v-else>{{ $t('no_season') }}</td>
                        </tr>
                        <tr>
                            <th>{{ $t('team.form.team_type.label') }}</th>
                            <td v-if="team.team_type">{{ team.team_type.name }}</td>
                            <td v-else>{{ $t('no_type') }}</td>
                        </tr>
                        <tr>
                            <th>{{ $t('team.form.remark.label') }}</th>
                            <td>{{ team.remark }}</td>
                        </tr>
                    </table>
                </div>
                <div>
                    <h3 class="uk-heading-line"><span>{{ $t('members') }}</span></h3>
                    <div class="uk-child-width-1-1" uk-grid>
                        <div>
                            <p v-if="team.season" v-html="$t('age_remark', { season : team.season.name, start : team.season.formatted_start_date, end : team.season.formatted_end_date})"></p>
                        </div>
                        <div v-if="members == null || members.length == 0">
                            {{ $t('no_members') }}
                        </div>
                        <div v-else>
                            <table class="uk-table uk-table-small uk-table-middle uk-table-divider">
                                <tr v-for="member in members" :key="member.id">
                                    <td>
                                        <input class="uk-checkbox" type="checkbox" v-model="selectedMembers" :value="member.id">
                                    </td>
                                    <td>
                                        <strong>{{ member.person.name }}</strong><br />
                                        {{ member.person.formatted_birthdate }} ({{ memberAge(member) }})
                                    </td>
                                    <td>
                                        {{ member.license }}<br />
                                        <fa-icon v-if="member.person.gender == 1" name="male" />
                                        <fa-icon v-if="member.person.gender == 2" name="female" />
                                        <fa-icon v-if="member.person.gender == 0" name="question" />
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <a v-if="team && $team.isAllowed('attachMember', team)" class="uk-icon-button" @click="showAddMemberDialog">
                                <fa-icon name="plus" />
                            </a>
                            <a v-if="selectedMembers.length > 0" uk-toggle="target: #delete-member" class="uk-icon-button uk-button-danger">
                                <fa-icon name="trash" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Page>
</template>

<script>
    import messages from '../lang';

    import Page from './Page.vue';
    import AreYouSure from '@/components/AreYouSure.vue';
    import UikitInputText from '@/components/uikit/InputText.vue';
    import UikitSelect from '@/components/uikit/Select.vue';

    import UIkit from 'uikit';

    import 'vue-awesome/icons/plus';
    import 'vue-awesome/icons/ban';
    import 'vue-awesome/icons/edit';
    import 'vue-awesome/icons/spinner';
    import 'vue-awesome/icons/male';
    import 'vue-awesome/icons/female';
    import 'vue-awesome/icons/question';

    import Member from '../models/Member';

    import teamStore from '@/apps/teams/store';

    export default {
        data() {
            return {
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
            Page,
            AreYouSure,
            UikitInputText,
            UikitSelect
        },
        i18n : messages,
        computed : {
            team() {
                return this.$store.getters['teamModule/team'](this.$route.params.id);
            },
            members() {
                return this.$store.getters['teamModule/members'](this.$route.params.id);
            },
            availableMembers() {
                return this.$store.getters['teamModule/availableMembers'];
            },
            error() {
                return this.$store.getters['teamModule/error'];
            },
            notAllowed() {
                return this.error && this.error.response.status == 401;
            },
            notFound() {
                return this.error && this.error.response.status == 404;
            }
        },
        beforeCreate() {
            if (!this.$store.state.teamModule) {
                this.$store.registerModule('teamModule', teamStore);
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
                this.$store.dispatch('teamModule/read', { id : this.$route.params.id })
                    .then(() => {
                        this.fetchMembers();
                    })
                    .catch((err) => {
                    });
            },
            fetchMembers() {
                this.$store.dispatch('teamModule/members', { id : this.$route.params.id })
                    .catch((err) => {
                    });
            },
            showAddMemberDialog() {
                if (this.team.team_type) {
                    this.$store.dispatch('teamModule/availableMembers', { id : this.$route.params.id })
                        .catch((err) => {
                            console.log(err);
                        });
                }
                var modal = UIkit.modal(this.$refs.addMemberDialog);
                modal.show();
            },
            hideAddMemberDialog() {
                var modal = UIkit.modal(this.$refs.addMemberDialog);
                modal.hide();
            },
            filterAvailableMembers() {
                this.$store.dispatch('teamModule/availableMembers', {
                    id : this.$route.params.id,
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
                    var member = new Member();
                    member.id = id;
                    members.push(member);
                });
                this.$store.dispatch('teamModule/addMembers', {
                    id : this.$route.params.id,
                    members : members
                });
                var modal = UIkit.modal(this.$refs.addMemberDialog);
                modal.hide();
            },
            deleteMembers() {
                var members = [];
                this.selectedMembers.forEach((id) => {
                    var member = new Member();
                    member.id = id;
                    members.push(member);
                });
                this.$store.dispatch('teamModule/deleteMembers', {
                    id : this.$route.params.id,
                    members : members
                });
                this.selectedMembers = [];
            },
            memberAge(member) {
                if (this.team.season) {
                    return this.team.season.end_date.diff(member.person.birthdate, 'years');
                }
                return member.person.age;
            }
        }
    };
</script>
