<template>
  <!-- eslint-disable max-len -->
  <div>
    <AreYouSure
      id="delete-member"
      :yes="$t('delete')"
      :no="$t('cancel')"
      @sure="deleteMembers"
    >
      <template slot="title">
        {{ $t('delete') }}
      </template>
      {{ $t('sure_to_delete') }}
    </AreYouSure>
    <AddMembersDialog
      v-if="team"
      id="add-member-dialog"
      :team="team"
    />
    <div
      v-if="notAllowed"
      class="uk-alert-danger"
      uk-alert
    >
      {{ $t('not_allowed') }}
    </div>
    <div
      v-if="notFound"
      class="uk-alert-danger"
      uk-alert
    >
      {{ $t('not_found') }}
    </div>
    <Spinner v-if="$wait.is('teams.read')" />
    <div
      v-if="team"
      uk-grid
    >
      <div class="uk-width-1-1@s uk-width-1-2@m">
        <table class="uk-table uk-table-striped">
          <tr>
            <th>
              {{ $t('name') }}
            </th>
            <td>
              {{ team.name }}
            </td>
          </tr>
          <tr>
            <th>
              {{ $t('form.team.season.label') }}
            </th>
            <td v-if="team.season">
              <router-link
                :to="{ name: 'seasons.read', params: { id : team.season.id } }"
              >
                {{ team.season.name }}
              </router-link>
            </td>
            <td v-else>
              {{ $t('no_season') }}
            </td>
          </tr>
          <tr>
            <th>{{ $t('form.team.team_type.label') }}</th>
            <td v-if="team.team_type">
              <router-link
                :to="{ name: 'team_types.read', params: { id : team.team_type.id } }"
              >
                {{ team.team_type.name }}
              </router-link>
            </td>
            <td v-else>
              {{ $t('no_type') }}
            </td>
          </tr>
          <tr>
            <th>
              {{ $t('form.team.remark.label') }}
            </th>
            <td>
              {{ team.remark }}
            </td>
          </tr>
        </table>
      </div>
      <div class="uk-width-1-1@s uk-width-1-2@m">
        <h3 class="uk-heading-line">
          <span>{{ $t('members') }}</span>
        </h3>
        <div uk-grid>
          <div
            v-if="team.season"
            class="uk-width-1-1"
          >
            <p
              class="uk-text-meta"
              v-html="$t('age_remark', { season : team.season.name, start : team.season.formatted_start_date, end : team.season.formatted_end_date})"
            >
            </p>
          </div>
          <div
            v-if="hasMembers"
            class="uk-width-1-1"
          >
            {{ $t('count') }} : {{members.length}}
          </div>
          <div
            v-else
            class="uk-width-1-1"
          >
            {{ $t('no_members') }}
          </div>
          <div
            v-if="members && members.length > 10"
            class="uk-width-1-1"
          >
            <a
              v-if="team && $can('attachMember', team)"
              uk-toggle="target: #add-member-dialog"
              class="uk-icon-button uk-link-reset"
            >
              <i class="fas fa-plus"></i>
            </a>
            <a
              v-if="selectedMembers.length > 0"
              uk-toggle="target: #delete-member"
              class="uk-icon-button uk-link-reset uk-button-danger"
            >
              <i class="fas fa-trash" style="color:#fff"></i>
            </a>
          </div>
          <div
            v-if="members && members.length > 0"
            class="uk-width-1-1"
          >
            <table class="uk-table uk-table-small uk-table-middle uk-table-divider">
              <tr
                v-for="member in members"
                :key="member.id"
              >
                <td>
                  <input
                    class="uk-checkbox"
                    type="checkbox"
                    v-model="selectedMembers"
                    :value="member.id"
                  />
                </td>
                <td>
                  <strong>
                    {{ member.person.name }}
                  </strong>
                  <br />
                  {{ member.person.formatted_birthdate }}
                  &nbsp;({{ memberAge(member) }})
                </td>
                <td>
                  {{ member.license }}
                  <br />
                  <i v-if="member.person.gender == 1" class="fas fa-male"></i>
                  <i v-if="member.person.gender == 2" class="fas fa-female"></i>
                  <i v-if="member.person.gender == 0" class="fas fa-question"></i>
                </td>
              </tr>
            </table>
          </div>
          <div class="uk-width-1-1">
            <a
              v-if="team && $can('attachMember', team)"
              uk-toggle="target: #add-member-dialog"
              class="uk-icon-button uk-link-reset"
            >
              <i class="fas fa-plus"></i>
            </a>
            <a
              v-if="selectedMembers.length > 0"
              uk-toggle="target: #delete-member"
              class="uk-icon-button uk-button-danger uk-link-reset"
            >
              <i class="fas fa-trash uk-light"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import messages from './lang';

import AddMembersDialog from './AddMembersDialog.vue';
import AreYouSure from '@/components/AreYouSure.vue';
import Spinner from '@/components/Spinner.vue';

import Member from '@/models/Member';

export default {
  components: {
    AreYouSure,
    AddMembersDialog,
    Spinner
  },
  i18n: messages,
  data() {
    return {
      selectedMembers: []
    };
  },
  computed: {
    team() {
      return this.$store.getters['team/team'](this.$route.params.id);
    },
    members() {
      return this.$store.getters['team/members'](this.$route.params.id);
    },
    hasMembers() {
      return this.members !== null && this.members.length > 0;
    },
    error() {
      return this.$store.state.team.error;
    },
    notAllowed() {
      return this.error && this.error.response.status === 401;
    },
    notFound() {
      return this.error && this.error.response.status === 404;
    }
  },
  beforeRouteEnter(to, from, next) {
    next(async(vm) => {
      await vm.fetchData(to.params);
      next();
    });
  },
  async beforeRouteUpdate(to, from, next) {
    await this.fetchData(to.params);
    next();
  },
  methods: {
    async fetchData(params) {
      await this.$store.dispatch('team/read', {
        id: params.id
      });
      await this.$store.dispatch('team/members', {
        id: params.id
      });
    },
    showAddMemberDialog() {
      if (this.team.team_type) {
        this.$store.dispatch('team/availableMembers', {
          id: this.$route.params.id
        }).catch((err) => {
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
      this.$store.dispatch('team/deleteMembers', {
        id: this.$route.params.id,
        members: members
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
