<template>
  <div v-if="team">
    <h2 class="header-line">Leden</h2>
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
      {{ $t('count') }} : {{ team.members.length }}
    </div>
    <Alert
      v-else
      type="info"
      class="mb-4"
    >
      {{ $t('no_members') }}
    </Alert>
    <div
      v-if="hasMembers && team.members.length > 10"
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
      v-if="hasMembers"
      class="mb-4"
    >
      <table
        class="table table-striped"
      >
        <thead>
          <tr>
            <th>
            </th>
            <th scope="col">
              {{ $t('member.name')}}<br />
              {{ $t('member.birthdate')}} ({{ $t('member.age')}})
            </th>
            <th scope="col">
              {{ $t('member.license')}}<br />
              {{ $t('member.gender')}}
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="member in team.members"
            :key="member.id"
          >
            <td class="checkbox_cell">
              <input
                type="checkbox"
                v-model="selectedMembers"
                :value="member.id"
              />
            </td>
            <td class="text-sm">
              <strong>
                {{ member.person.name }}
              </strong>
              <br />
              {{ member.person.formatted_birthdate }}
              &nbsp;({{ memberAge(member) }})
            </td>
            <td class="text-sm">
              {{ member.license }}
              <br />
              <i v-if="member.person.gender == 1" class="fas fa-male"></i>
              <i v-if="member.person.gender == 2" class="fas fa-female"></i>
              <i v-if="member.person.gender == 0" class="fas fa-question"></i>
            </td>
          </tr>
        </tbody>
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

<style scoped>
.checkbox_cell {
  @apply align-middle !important;
}
</style>

<script>
import messages from './lang';

import AddMembersDialog from './AddMembersDialog.vue';
import AreYouSure from '@/components/AreYouSure.vue';
import Alert from '@/components/Alert';

import Member from '@/models/Member';

export default {
  components: {
    AddMembersDialog,
    AreYouSure,
    Alert,
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
      return this.$store.state.team.active;
    },
    hasMembers() {
      return this.team && this.team.members != null
        && this.team.members.length > 0;
    },
    seasonAgeRemark() {
      return this.$t('age_remark', {
        season: this.team.season.name,
        start: this.team.season.formatted_start_date,
        end: this.team.season.formatted_end_date
      });
    },
  },
  methods: {
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
