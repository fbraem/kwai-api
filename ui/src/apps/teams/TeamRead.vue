<template>
  <!-- eslint-disable max-len -->
  <div class="container mt-4 mx-auto">
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
    <TeamCard
      v-if="team"
      :team="team"
      :complete="true"
    />
    <div
      v-if="team"
      class="flex flex-col w-full my-4 mx-auto md:w-2/3"
    >
      <h3 class="header-line">
        <span>{{ $t('members') }}</span>
      </h3>
      <Alert
        v-if="team.season"
        type="warning"
        class="mb-4"
      >
        <div v-html="seasonAgeRemark"></div>
      </Alert>
      <div
        v-if="hasMembers"
        class="mb-4"
      >
        {{ $t('count') }} : {{members.length}}
      </div>
      <Alert
        v-else
        type="info"
        class="mb-4"
      >
        {{ $t('no_members') }}
      </Alert>
      <div
        v-if="members && members.length > 10"
        class="mb-4"
      >
        <a
          v-if="team && $can('attachMember', team)"
          class="icon-button text-gray-700 hover:bg-gray-300"
          @click.prevent.stop="showIt"
        >
          <i class="fas fa-plus"></i>
        </a>
        <a
          v-if="selectedMembers.length > 0"
          class="icon-button text-red-700 hover:bg-red-300"
          @click="showDeleteMemberDialog = true;"
        >
          <i class="fas fa-trash"></i>
        </a>
      </div>
      <div
        v-if="members && members.length > 0"
        class="mb-4"
      >
        <table class="w-full border-collapse text-left">
          <tr class="bg-gray-500 border-b border-gray-200">
            <th class="py-2 px-3 font-bold uppercase text-sm text-white">
            </th>
            <th class="py-2 px-3 font-bold uppercase text-sm text-white">
              {{ $t('member.name')}}<br />
              {{ $t('member.birthdate')}} ({{ $t('member.age')}})
            </th>
            <th class="py-2 px-3 font-bold uppercase text-sm text-white">
              {{ $t('member.license')}}<br />
              {{ $t('member.gender')}}
            </th>
          </tr>
          <tr
            v-for="member in members"
            :key="member.id"
            class="odd:bg-gray-200 border-b border-gray-400"
          >
            <td class="py-2 px-3 text-sm text-gray-700">
              <input
                type="checkbox"
                v-model="selectedMembers"
                :value="member.id"
              />
            </td>
            <td class="py-2 px-3 text-sm text-gray-700">
              <strong>
                {{ member.person.name }}
              </strong>
              <br />
              {{ member.person.formatted_birthdate }}
              &nbsp;({{ memberAge(member) }})
            </td>
            <td class="py-2 px-3 text-sm text-gray-700">
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
          class="icon-button text-gray-700 hover:bg-gray-300"
          @click.prevent.stop="showIt"
        >
          <i class="fas fa-plus"></i>
        </a>
        <a
          v-if="selectedMembers.length > 0"
          class="icon-button text-red-700 hover:bg-red-300"
        >
          <i class="fas fa-trash"></i>
        </a>
      </div>
    </div>
    <AddMembersDialog
      v-if="team"
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
import TeamCard from './TeamCard';

import Member from '@/models/Member';

export default {
  components: {
    AddMembersDialog,
    Spinner,
    AreYouSure,
    Alert,
    TeamCard
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
    }
  }
};
</script>
