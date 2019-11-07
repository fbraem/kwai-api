<template>
  <!-- eslint-disable max-len -->
  <div
    v-if="training"
    class="mt-6 flex justify-center"
  >
    <TrainingCard :training="training">
      <div class="border-t border-gray-300 p-6">
        <h1>
          {{ $t('training.presences.attendees') }}
        </h1>
        <div
          v-if="hasPresences"
          class="flex flex-col mb-6"
        >
          <div
            v-for="member in presences"
            :key="member.id"
            class="flex flex-row px-2 py-2 first:border-t odd:bg-gray-200 border-b border-gray-400">
            <div class="flex-grow">
              <MemberSummary :member="member" />
            </div>
            <div class="flex-none">
              <a class="icon-button text-red-700 hover:bg-gray-300">
                <i
                  class="fas fa-times"
                  @click="removePresence(member)"
                >
                </i>
              </a>
            </div>
          </div>
        </div>
        <p
          v-else
          class="mb-6"
        >
          {{ $t('training.presences.nobody') }}
        </p>
        <h1 class="mb-6">
          {{ $t('training.presences.possible') }}
        </h1>
        <p class="mb-6 text-sm">
          {{ $t('training.presences.team') }}
        </p>
        <div class="flex flex-col mb-6">
          <div class="flex flex-row px-2 py-2 first:border-t odd:bg-gray-200 border-b border-gray-400"
            v-for="member in teamMembers"
            :key="member.id"
          >
            <div class="flex-grow">
              <MemberSummary :member="member" />
            </div>
            <div class="flex-none">
              <a class="icon-button text-gray-700 hover:bg-gray-300">
                <i
                  class="fas fa-plus"
                  @click="addPresence(member)"
                >
                </i>
              </a>
            </div>
          </div>
        </div>
        <KwaiForm
          :form="form"
          class="mb-6 bg-gray-200 p-4"
        >
          <KwaiField
            name="otherMembers"
            :label="$t('training.presences.form.other_members.label')"
            class="max-w-lg md:max-w-2xl"
          >
            <div class="relative">
              <multiselect
                :options="otherMembers"
                label="name"
                track-by="id"
                :multiple="true"
                :close-on-select="false"
                :selectLabel="$t('training.presences.form.select_label')"
                :deselectLabel="$t('training.presences.form.deselect_label')"
              >
              <template slot="tag" slot-scope="{ option, remove }">
                <span>
                  <span>{{ option.name }} - </span>
                  <span @click="remove(option)">❌</span>
                  <br />
                </span>
              </template>
              </multiselect>
            </div>
          </KwaiField>
          <div class="flex justify-end mt-6">
            <button
              class="red-button disabled:opacity-50 disabled:cursor-not-allowed"
              :disabled="disableAddOthers"
              @click.prevent.stop="addAttendeeFromList"
            >
              {{ $t('training.presences.form.add_others') }}
            </button>
          </div>
        </KwaiForm>
      </div>
    </TrainingCard>
  </div>
</template>

<script>
import messages from './lang';

import TrainingCard from './TrainingCard';
import MemberSummary from '@/apps/members/components/MemberSummary';

import makeForm, { makeField } from '@/js/Form';
import KwaiForm from '@/components/forms/KwaiForm';
import KwaiField from '@/components/forms/KwaiField';
import Multiselect from '@/components/forms/MultiSelect';

export default {
  i18n: messages,
  components: {
    TrainingCard, MemberSummary, KwaiForm, KwaiField, Multiselect
  },
  data() {
    return {
      presences: [],
      form: makeForm({
        otherMembers: makeField({
          value: []
        })
      })
    };
  },
  computed: {
    training() {
      return this.$store.getters['training/training'](
        this.$route.params.id
      );
    },
    hasPresences() {
      return this.presences.length > 0;
    },
    teamMembers() {
      // TODO: For the moment only one team!
      if (this.training.teams.length > 0) {
        let teamId = this.training.teams[0].id;
        // Create a copy of the members array
        let members = [ ... this.$store.getters['team/members'](teamId) || []];
        if (members.length > 0) {
          this.presences.forEach((p) => {
            let index = members.findIndex(o => o.id === p.id);
            if (index !== -1) {
              members.splice(index, 1);
            }
          });
        }
        return members;
      }
      return [];
    },
    otherMembers() {
      var others = this.$store.state.member.members || [];
      this.presences.forEach((p) => {
        let index = others.findIndex(o => o.id === p.id);
        if (index !== -1) {
          others.splice(index, 1);
        }
      });
      return others;
    },
    disableAddOthers() {
      return this.form.fields.otherMembers.value.length === 0;
    }
  },
  beforeRouteEnter(to, from, next) {
    next(async(vm) => {
      await vm.fetchData(to.params.id);
      next();
    });
  },
  async beforeRouteUpdate(to, from, next) {
    await this.fetchData(to.params.id);
    next();
  },
  methods: {
    async fetchData(id) {
      await this.$store.dispatch('training/read', {
        id,
        cache: false
      }).catch((err) => {
        console.log(err);
      });
      if (this.training) {
        this.presences = this.training.presences ?
          [ ... this.training.presences ] : [];
      }
      if (this.training && this.training.teams.length > 0) {
        // TODO: For the moment only one team!
        this.$store.dispatch('team/members', {
          id: this.training.teams[0].id
        });
      }
      this.$store.dispatch('member/browse');
    },
    removePresence(member) {
      this.presences = this.presences.filter(p => p.id !== member.id);
      this.saveAttendees();
    },
    addPresence(member) {
      this.presences.push(member);
      this.saveAttendees();
    },
    addAttendeeFromList() {
      this.form.fields.otherMembers.value.forEach((member) => {
        this.presences.push(member);
      });
      this.saveAttendees();
      this.form.fields.otherMembers.value = [];
    },
    saveAttendees() {
      this.$store.dispatch('training/updatePresences', {
        training: this.training,
        presences: this.presences
      });
    }
  }
};
</script>
