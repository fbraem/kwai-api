<template>
  <!-- eslint-disable max-len -->
  <Modal
    v-if="team"
    v-show="show"
    @close="$emit('close');"
  >
    <template slot="header">
      <h2>{{ $t('add_members') }}</h2>
      <p
        v-if="team.team_type"
        class="kwai-text-meta"
      >
        {{ $t('add_members_info') }}
      </p>
    </template>
    <slot>
      <KwaiForm
         v-if="!team.team_type"
         :form="form"
         :stacked="false"
      >
        <div style="display: flex; flex-direction: row; align-items: center; justify-content: space-around">
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
              class="primary:kwai-button"
              @click.prevent.stop="filterAvailableMembers"
            >
              {{ $t('filter') }}
            </button>
          </div>
        </div>
      </KwaiForm>
      <p
        v-if="! team.team_type"
        class="kwai-text-meta"
      >
        {{ $t('use_filter') }}
      </p>
      <p
        v-if="team.season && availableMembers.length > 0"
        class="kwai-text-meta" v-html="$t('age_remark', { season : team.season.name, start : team.season.formatted_start_date, end : team.season.formatted_end_date})"
      >
      </p>
      <hr />
      <div
        v-if="$wait.is('teams.availableMembers')">
        <Spinner />
      </div>
      <div style="margin-bottom:20px;max-height: 300px; overflow: auto;">
        <table
          v-if="availableMembers.length > 0"
          class="kwai-table kwai-table-small kwai-table-middle kwai-table-divider"
        >
          <tr>
            <th>
              <input
                class="kwai-checkbox"
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
                class="kwai-checkbox"
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
      <div>
        <hr />
        <button
          class="kwai-button"
          @click.prevent.stop="$emit('close');"
        >
          <i class="fas fa-ban"></i>&nbsp; {{ $t('cancel') }}
        </button>
        <button
          class="primary:kwai-button"
          :disabled="selectedAvailableMembers.length == 0"
          @click="addMembers">
          <i class="fas fa-plus"></i>&nbsp; {{ $t('add') }}
        </button>
      </div>
    </slot>
  </Modal>
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
import Modal from '@/components/Modal';

import messages from './lang';

import Member from '@/models/Member';

export default {
  components: {
    Spinner,
    Modal,
    KwaiForm,
    KwaiField,
    KwaiInputText,
    KwaiSelect
  },
  i18n: messages,
  props: [ 'team', 'show' ],
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
    },
    team(nv) {
      if (nv) {
        this.selectedAvailableMembers = [];
        if (nv.team_type) {
          this.$store.dispatch('team/availableMembers', {
            id: nv.id
          }).catch((err) => {
            console.log(err);
          });
        }
      }
    }
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
      this.$emit('close');
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
