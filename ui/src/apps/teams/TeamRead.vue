<template>
  <!-- eslint-disable max-len -->
  <div class="page-container">
    <Alert
      v-if="notAllowed"
      type="danger"
    >
      {{ $t('not_allowed') }}
    </Alert>
    <Alert
      v-if="notFound"
      type="danger"
    >
      {{ $t('not_found') }}
    </Alert>
    <Spinner v-if="$wait.is('teams.read')" />
    <div
      v-if="team"
      style="grid-column: span 2; display: flex; flex-direction: column;"
    >
      <table class="kwai-table kwai-table-striped">
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
              :to="seasonLink"
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
              :to="teamTypeLink"
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
      <div style="display: flex; flex-direction: column;">
        <h3 class="kwai-header-line">
          <span>{{ $t('members') }}</span>
        </h3>
        <div
          v-if="team.season"
        >
          <p
            class="kwai-text-meta"
            v-html="seasonAgeRemark"
          >
          </p>
        </div>
        <div
          v-if="hasMembers"
        >
          {{ $t('count') }} : {{members.length}}
        </div>
        <div
          v-else
        >
          {{ $t('no_members') }}
        </div>
        <div
          v-if="members && members.length > 10"
        >
          <a
            v-if="team && $can('attachMember', team)"
            class="kwai-icon-button"
            @click.prevent.stop="showIt"
          >
            <i class="fas fa-plus"></i>
          </a>
          <a
            v-if="selectedMembers.length > 0"
            class="danger:kwai-icon-button"
            @click="showDeleteMemberDialog = true;"
          >
            <i class="fas fa-trash"></i>
          </a>
        </div>
        <div
          v-if="members && members.length > 0"
        >
          <table class="kwai-table kwai-table-small kwai-table-middle kwai-table-divider">
            <tr
              v-for="member in members"
              :key="member.id"
            >
              <td>
                <input
                  class="kwai-checkbox"
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
        <div>
          <a
            v-if="team && $can('attachMember', team)"
            class="kwai-icon-button"
            @click.prevent.stop="showIt"
          >
            <i class="fas fa-plus"></i>
          </a>
          <a
            v-if="selectedMembers.length > 0"
            class="danger:kwai-icon-button"
          >
            <i class="fas fa-trash"></i>
          </a>
        </div>
      </div>
    </div>
    <AddMembersDialog
      :show="showAddMemberDialog"
      :team="team"
      @close="showAddMemberDialog = false;"
    />
    <AreYouSure
      :show="showDeleteMemberDialog"
      :yes="$t('delete')"
      :no="$t('cancel')"
      @sure="deleteMembers"
      @close="showDeleteMemberDialog = false;"
    >
      <template slot="title">
        {{ $t('delete') }}
      </template>
      {{ $t('sure_to_delete') }}
    </AreYouSure>
  </div>
</template>

<script>
import messages from './lang';

import AddMembersDialog from './AddMembersDialog.vue';
import Spinner from '@/components/Spinner.vue';
import AreYouSure from '@/components/AreYouSure.vue';
import Alert from '@/components/Alert';

import Member from '@/models/Member';

export default {
  components: {
    AddMembersDialog,
    Spinner,
    AreYouSure,
    Alert
  },
  i18n: messages,
  data() {
    return {
      selectedMembers: [],
      showAddMemberDialog: false,
      showDeleteMemberDialog: false
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
    },
    seasonAgeRemark() {
      return this.$t('age_remark', {
        season: this.team.season.name,
        start: this.team.season.formatted_start_date,
        end: this.team.season.formatted_end_date
      });
    },
    teamTypeLink() {
      return {
        name: 'team_types.read',
        params: {
          id: this.team.team_type.id
        }
      };
    },
    seasonLink() {
      return {
        name: 'seasons.read',
        params: {
          id: this.team.season.id
        }
      };
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
    },
    showIt() {
      this.showAddMemberDialog = true;
      console.log('hey');
    }
  }
};
</script>
