<template>
  <!-- eslint-disable max-len -->
  <div :id="id" uk-modal ref="addMemberDialog">
    <div class="uk-modal-dialog uk-modal-body">
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
              <field name="start_age" :label="$t('min_age')">
                <uikit-input-text />
              </field>
            </div>
            <div>
              <field name="end_age" :label="$t('max_age')">
                <uikit-input-text />
              </field>
            </div>
            <div>
              <field name="gender" :label="$t('gender')">
                <uikit-select :items="genders" />
              </field>
            </div>
            <div>
              <label class="uk-form-label">&nbsp;</label>
              <button class="uk-button uk-button-primary" @click="filterAvailableMembers">
                {{ $t('filter') }}
              </button>
            </div>
          </form>
          <p v-if="! team.team_type" class="uk-text-meta">
            {{ $t('use_filter') }}
          </p>
          <p class="uk-text-meta" v-if="team.season && availableMembers.length > 0" v-html="$t('age_remark', { season : team.season.name, start : team.season.formatted_start_date, end : team.season.formatted_end_date})"></p>
          <hr />
        </div>
        <div v-if="$wait.is('teams.availableMembers')" class="uk-flex-center" uk-grid>
          <div class="uk-text-center">
            <i class="fas fa-spinner fa-2x fa-spin"></i>
          </div>
        </div>
        <div class="uk-overflow-auto uk-height-medium">
          <table v-if="availableMembers.length > 0" class="uk-table uk-table-small uk-table-middle uk-table-divider">
            <tr v-for="member in availableMembers" :key="member.id">
              <td>
                <input class="uk-checkbox" type="checkbox" v-model="selectedAvailableMembers" :value="member.id" />
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
          <p v-else-if="team.team_type">
            {{ $t('no_available_members') }}
          </p>
        </div>
        <div>
          <hr />
          <button class="uk-button uk-button-default" @click="hideAddMemberDialog">
            <i class="fas fa-ban"></i>&nbsp; {{ $t('cancel') }}
          </button>
          <button class="uk-button uk-button-primary" :disabled="selectedAvailableMembers.length == 0" @click="addMembers">
            <i class="fas fa-plus"></i>&nbsp; {{ $t('add') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import AddMembersDialog from './AddMembersDialog';

import Field from '@/components/forms/Field.vue';
import UikitInputText from '@/components/forms/InputText.vue';
import UikitSelect from '@/components/forms/Select.vue';

import UIkit from 'uikit';

import messages from './lang';

import Member from '@/models/Member';

export default {
  components: {
    Field,
    UikitInputText,
    UikitSelect
  },
  i18n: messages,
  props: [ 'id', 'team' ],
  mixins: [ AddMembersDialog ],
  data() {
    return {
      selectedAvailableMembers: [],
      genders: [
        { text: 'None', value: 0 },
        { text: 'Male', value: 1 },
        { text: 'Female', value: 2 },
      ]
    };
  },
  computed: {
    availableMembers() {
      return this.$store.state.team.availableMembers;
    }
  },
  mounted() {
    UIkit.util.on('#' + this.id, 'beforeshow', () => {
      this.selectedAvailableMembers = [];
      if (this.team.team_type) {
        this.$store.dispatch('team/availableMembers', {
          id: this.team.id
        }).catch((err) => {
          console.log(err);
        });
      }
    });
  },
  methods: {
    filterAvailableMembers() {
      this.$store.dispatch('team/availableMembers', {
        id: this.$route.params.id,
        filter: {
          start_age: this.form.start_age.value,
          end_age: this.form.end_age.value,
          gender: this.form.gender.value
        }
      });
    },
    hideAddMemberDialog() {
      var modal = UIkit.modal(this.$refs.addMemberDialog);
      modal.hide();
    },
    addMembers() {
      var members = [];
      this.selectedAvailableMembers.forEach((id) => {
        var member = new Member();
        member.id = id;
        members.push(member);
      });
      this.$store.dispatch('team/addMembers', {
        id: this.$route.params.id,
        members: members
      });
      var modal = UIkit.modal(this.$refs.addMemberDialog);
      modal.hide();
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
