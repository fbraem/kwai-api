<template>
  <div
    v-if="training"
    class="page-container"
    style="display: flex; justify-content: center;"
  >
    <TrainingCard :training="training">
      <div class="training-area">
        <h1>
          {{ $t('training.presences.attendees') }}
        </h1>
        <table
          v-if="hasPresences"
          class="kwai-table kwai-table-small kwai-table-striped"
        >
          <tr
            v-for="member in presences"
            :key="member.id"
          >
            <td>
              <MemberSummary :member="member" />
            </td>
            <td class="kwai-table-shrink">
              <a class="kwai-icon-button">
                <i
                  class="fas fa-times"
                  @click="removePresence(member)"
                >
                </i>
              </a>
            </td>
          </tr>
        </table>
        <p v-else>
          {{ $t('training.presences.nobody') }}
        </p>
        <WarningComponent v-if="changed" @save="saveAttendees" />
        <h1>
          {{ $t('training.presences.possible') }}
        </h1>
        <p class="kwai-text-meta">
          {{ $t('training.presences.team') }}
        </p>
        <table class="kwai-table kwai-table-small kwai-table-striped">
          <tr
            v-for="member in teamMembers"
            :key="member.id"
          >
            <td>
              <MemberSummary :member="member" />
            </td>
            <td class="kwai-table-shrink">
              <a class="kwai-icon-button">
                <i
                  class="fas fa-plus"
                  @click="addPresence(member)"
                >
                </i>
              </a>
            </td>
          </tr>
        </table>
        <KwaiForm
          :form="form"
          style="margin-top: 20px;"
        >
          <!-- TODO: find a better way to calc max-width -->
          <KwaiField
            name="otherMembers"
            :label="$t('training.presences.form.other_members.label')"
            style="max-width: 500px;"
          >
            <div style="position:relative;">
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
          <button
            class="kwai-button"
            :disabled="disableAddOthers"
            @click.prevent.stop="addAttendeeFromList"
            style="align-self: center;"
          >
            {{ $t('training.presences.form.add_others') }}
          </button>
        </KwaiForm>
        <WarningComponent v-if="changed" @save="saveAttendees" />
      </div>
    </TrainingCard>
  </div>
</template>

<style scoped>
.training-area {
  border-top: 1px solid var(--kwai-color-default-light);
  padding: 40px;
}
</style>

<script>
import messages from './lang';

import TrainingCard from './TrainingCard';
import MemberSummary from '@/apps/members/components/MemberSummary';

import makeForm, { makeField } from '@/js/Form';
import KwaiForm from '@/components/forms/KwaiForm';
import KwaiField from '@/components/forms/KwaiField';
import Multiselect from '@/components/forms/MultiSelect';

const WarningComponent = {
  i18n: messages,
  render(h) {
    return h('div', {
    }, [
      h('div', {
      }, [
        h('div', {
        },
        this.$t('training.presences.save_warning')
        ),
        h('div', {},
          [
            h('button', {
              class: {
                'primary:kwai-button': true,
              },
              on: {
                click: () => { this.$emit('save'); }
              }
            }, this.$t('training.presences.save')),
          ]
        ),
      ]),
    ]);
  }
};

export default {
  i18n: messages,
  components: {
    TrainingCard, MemberSummary, KwaiForm, KwaiField, Multiselect,
    WarningComponent
  },
  data() {
    return {
      presences: [],
      form: makeForm({
        otherMembers: makeField({
          value: []
        })
      }),
      changed: false
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
        let members = this.$store.getters['team/members'](teamId) || [];
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
      this.changed = true;
    },
    addPresence(member) {
      this.presences.push(member);
      this.changed = true;
    },
    addAttendeeFromList() {
      this.form.fields.otherMembers.value.forEach((member) => {
        this.presences.push(member);
      });
      this.form.fields.otherMembers.value = [];
      this.changed = true;
    },
    saveAttendees() {
      this.$store.dispatch('training/updatePresences', {
        training: this.training,
        presences: this.presences
      });
      this.changed = false;
    }
  }
};
</script>
