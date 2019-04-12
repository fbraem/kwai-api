<template>
  <!-- eslint-disable max-len -->
  <div
    :id="id"
    uk-modal
    ref="addMemberDialog"
  >
    <div class="uk-modal-dialog uk-modal-body">
      <div class="uk-grid-small" uk-grid>
        <div class="uk-width-1-1">
          <h2 class="uk-modal-title">
            {{ $t('add_members') }}
          </h2>
          <p
            v-if="team.team_type"
            class="uk-text-meta"
          >
            {{ $t('add_members_info') }}
          </p>
        </div>
        <div class="uk-width-1-1">
          <KwaiForm
             v-if="!team.team_type"
             :form="form"
             :stacked="false"
          >
            <div class="uk-child-width-1-4 uk-flex-middle" uk-grid>
              <div>
                <KwaiField
                  name="start_age"
                  :label="$t('min_age')"
                >
                  <KwaiInputText />
                </KwaiField>
              </div>
              <div>
                <KwaiField
                  name="end_age"
                  :label="$t('max_age')"
                >
                  <KwaiInputText />
                </KwaiField>
              </div>
              <div>
                <KwaiField
                  name="gender"
                  :label="$t('gender')"
                >
                  <KwaiSelect :items="genders" />
                </KwaiField>
              </div>
              <div>
                <label class="uk-form-label">
                  &nbsp;
                </label>
                <button
                  class="uk-button uk-button-primary"
                  @click.prevent.stop="filterAvailableMembers"
                >
                  {{ $t('filter') }}
                </button>
              </div>
            </div>
          </KwaiForm>
          <p
            v-if="! team.team_type"
            class="uk-text-meta"
          >
            {{ $t('use_filter') }}
          </p>
          <p
            v-if="team.season && availableMembers.length > 0"
            class="uk-text-meta" v-html="$t('age_remark', { season : team.season.name, start : team.season.formatted_start_date, end : team.season.formatted_end_date})"
          >
          </p>
          <hr />
        </div>
        <div
          v-if="$wait.is('teams.availableMembers')"
          class="uk-width-1-1">
          <Spinner />
        </div>
        <div class="uk-width-1-1 uk-overflow-auto uk-height-medium">
          <table
            v-if="availableMembers.length > 0"
            class="uk-table uk-table-small uk-table-middle uk-table-divider"
          >
            <tr>
              <th>
                <input
                  class="uk-checkbox"
                  type="checkbox"
                  v-model="selectAll"
                />
              </th>
              <th>
                {{ $t('member.name')}}<br />
                {{ $t('member.birthdate')}} ({{ $t('member.age')}})
              </th>
              <th>
                {{ $t('member.license')}}<br />
                {{ $t('member.gender')}}
              </th>
            </tr>
            <tr
              v-for="member in availableMembers"
              :key="member.id"
            >
              <td>
                <input
                  class="uk-checkbox"
                  type="checkbox"
                  v-model="selectedAvailableMembers"
                  :value="member.id"
                />
              </td>
              <td>
                <strong>
                  {{ member.person.name }}
                </strong>
                <br />
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
        <div class="uk-width-1-1">
          <hr />
          <button
            class="uk-button uk-button-default"
            @click="hideAddMemberDialog"
          >
            <i class="fas fa-ban"></i>&nbsp; {{ $t('cancel') }}
          </button>
          <button
            class="uk-button uk-button-primary"
            :disabled="selectedAvailableMembers.length == 0"
            @click="addMembers">
            <i class="fas fa-plus"></i>&nbsp; {{ $t('add') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import makeForm, { makeField } from '@/js/Form';
const makeAddMembersForm = (fields) => {
  const writeForm = (filter) => {
    fields.start_age.value = filter.start_age;
    fields.end_age.value = filter.end_age;
    fields.gender.value = filter.gender;
  };
  const readForm = (filter) => {
    filter.start_age = fields.start_age.value;
    filter.end_age = fields.end_age.value;
    filter.gender = fields.gender.value;
  };
  return { ...makeForm(fields), writeForm, readForm };
};

import KwaiForm from '@/components/forms/KwaiForm.vue';
import KwaiField from '@/components/forms/KwaiField.vue';
import KwaiInputText from '@/components/forms/KwaiInputText.vue';
import KwaiSelect from '@/components/forms/KwaiSelect.vue';
import Spinner from '@/components/Spinner';

import UIkit from 'uikit';

import messages from './lang';

import Member from '@/models/Member';

export default {
  components: {
    Spinner,
    KwaiForm,
    KwaiField,
    KwaiInputText,
    KwaiSelect
  },
  i18n: messages,
  props: [ 'id', 'team' ],
  provide() {
    return {
      form: this.form
    };
  },
  data() {
    return {
      selectAll: false,
      selectedAvailableMembers: [],
      genders: [
        { text: 'None', value: 0 },
        { text: 'Male', value: 1 },
        { text: 'Female', value: 2 },
      ],
      form: makeAddMembersForm({
        start_age: makeField({
          value: 0
        }),
        end_age: makeField(),
        gender: makeField({
          value: 0
        })
      })
    };
  },
  computed: {
    availableMembers() {
      return this.$store.state.team.availableMembers;
    }
  },
  watch: {
    selectAll(nv) {
      this.selectedAvailableMembers = nv ?
        this.availableMembers.map((m, index) => m.id) : [];
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
          start_age: this.form.fields.start_age.value,
          end_age: this.form.fields.end_age.value,
          gender: this.form.fields.gender.value
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
