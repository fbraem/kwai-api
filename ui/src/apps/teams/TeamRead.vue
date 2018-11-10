<template>
    <div>
        <PageHeader>
            <div class="uk-grid">
                <div class="uk-width-1-1@s uk-width-5-6@m">
                    <h1 class="uk-h1">{{ $t('teams') }}</h1>
                    <h3 v-if="team" class="uk-h3 uk-margin-remove">{{ team.name }}</h3>
                </div>
                <div class="uk-width-1-1@s uk-width-1-6@m">
                    <div class="uk-flex uk-flex-right">
                        <div>
                            <router-link class="uk-icon-button uk-link-reset" :to="{ 'name' : 'teams.browse' }">
                                <i class="fas fa-list"></i>
                            </router-link>
                        </div>
                        <div class="uk-margin-small-left">
                            <router-link v-if="team && $team.isAllowed('update', team)" class="uk-icon-button uk-link-reset" :to="{ 'name' : 'teams.update', params : { id : team.id } }">
                                <i class="fas fa-edit"></i>
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </PageHeader>
        <section class="uk-section uk-section-small uk-container uk-container-expand">
            <AreYouSure id="delete-member" :yes="$t('delete')" :no="$t('cancel')" @sure="deleteMembers">
                <template slot="title">{{ $t('delete') }}</template>
                {{ $t('sure_to_delete') }}
            </AreYouSure>
            <AddMembersDialog v-if="team" id="add-member-dialog" :team="team"></AddMembersDialog>
            <div v-if="notAllowed" class="uk-alert-danger" uk-alert>
                {{ $t('not_allowed') }}
            </div>
            <div v-if="notFound" class="uk-alert-danger" uk-alert>
                {{ $t('not_found') }}
            </div>
            <div v-if="$wait.is('teams.read')" class="uk-flex-center" uk-grid>
                <div class="uk-text-center">
                    <i class="fas fa-spinner fa-2x fa-spin"></i>
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
                            <td v-if="team.season">
                                <router-link :to="{ name: 'seasons.read', params: { id : team.season.id } }">{{ team.season.name }}</router-link>
                            </td>
                            <td v-else>{{ $t('no_season') }}</td>
                        </tr>
                        <tr>
                            <th>{{ $t('team.form.team_type.label') }}</th>
                            <td v-if="team.team_type">
                                <router-link :to="{ name: 'team_types.read', params: { id : team.team_type.id } }">{{ team.team_type.name }}</router-link>
                            </td>
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
                        <div v-if="team.season">
                            <p class="uk-text-meta" v-html="$t('age_remark', { season : team.season.name, start : team.season.formatted_start_date, end : team.season.formatted_end_date})"></p>
                        </div>
                        <div v-if="members == null || members.length == 0">
                            {{ $t('no_members') }}
                        </div>
                        <div v-else-if="members">
                            {{ $t('count') }} : {{members.length}}
                        </div>
                        <div v-if="members && members.length > 10">
                            <a v-if="team && $team.isAllowed('attachMember', team)" uk-toggle="target: #add-member-dialog" class="uk-icon-button uk-link-reset">
                                <i class="fas fa-plus"></i>
                            </a>
                            <a v-if="selectedMembers.length > 0" uk-toggle="target: #delete-member" class="uk-icon-button uk-button-danger uk-link-reset">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                        <div v-if="members && members.length > 0">
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
                                        <i class="fas fa-male" v-if="member.person.gender == 1"></i>
                                        <i class="fas fa-female" v-if="member.person.gender == 2"></i>
                                        <i class="fas fa-question" v-if="member.person.gender == 0"></i>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <a v-if="team && $team.isAllowed('attachMember', team)" uk-toggle="target: #add-member-dialog" class="uk-icon-button uk-link-reset">
                                <i class="fas fa-plus"></i>
                            </a>
                            <a v-if="selectedMembers.length > 0" uk-toggle="target: #delete-member" class="uk-icon-button uk-button-danger uk-link-reset">
                                <i class="fas fa-trash uk-light"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
    import messages from './lang';

    import AddMembersDialog from './AddMembersDialog.vue';
    import PageHeader from '@/site/components/PageHeader.vue';
    import AreYouSure from '@/components/AreYouSure.vue';

    import UIkit from 'uikit';

    import Member from '@/models/Member';

    import teamStore from '@/stores/teams';

    export default {
        components : {
            PageHeader,
            AreYouSure,
            AddMembersDialog,
        },
        i18n : messages,
        data() {
            return {
                selectedMembers : []
            };
        },
        computed : {
            team() {
                return this.$store.getters['teamModule/team'](this.$route.params.id);
            },
            members() {
                return this.$store.getters['teamModule/members'](this.$route.params.id);
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
