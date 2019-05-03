<template>
  <div
    v-if="training"
    class="uk-flex-center"
    uk-grid
  >
    <div class="uk-width-1-1@s uk-width-2-3@m">
      <div class="uk-card uk-card-default">
        <div class="uk-card-header uk-padding-remove">
          <TrainingDayHour :training="training" />
          <h1 class="uk-margin-small-top uk-margin-bottom uk-text-center">
            {{ training.content.title }}
          </h1>
        </div>
        <div class="uk-card-body">
          <h1 class="uk-margin-small">
            {{ $t('training.presences.attendees') }}
          </h1>
          <table
            v-if="hasPresences"
            class="uk-table uk-table-small uk-table-striped"
          >
            <tr
              v-for="member in presences"
              :key="member.id"
            >
              <td>
                <MemberSummary :member="member" />
              </td>
              <td class="uk-table-shrink">
                <a class="uk-icon-button uk-link-reset">
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
          <p class="uk-text-meta">
            {{ $t('training.presences.team') }}
          </p>
          <table class="uk-table uk-table-small uk-table-striped">
            <tr
              v-for="member in members"
              :key="member.id"
            >
              <td>
                <MemberSummary :member="member" />
              </td>
              <td class="uk-table-shrink">
                <a class="uk-icon-button uk-link-reset">
                  <i
                    class="fas fa-plus"
                    @click="addPresence(member)"
                  >
                  </i>
                </a>
              </td>
            </tr>
          </table>
          <div class="uk-width-1-1">
            <KwaiForm
              :form="form"
            >
              <div
                class="uk-flex uk-flex-bottom"
                uk-grid
              >
                <div class="uk-width-expand">
                  <KwaiField
                    name="otherMembers"
                    :label="$t('training.presences.form.other_members.label')"
                  >
                    <multiselect
                      :options="otherMembers"
                      label="name"
                      track-by="id"
                      :multiple="true"
                      :close-on-select="false"
                      :selectLabel="$t('training.presences.form.select_label')"
                      :deselectLabel="$t('training.presences.form.deselect_label')"
                    />
                  </KwaiField>
                </div>
                <div>
                  <button
                    class="uk-button uk-button-default"
                    :disabled="disableAddOthers"
                    @click.prevent.stop="addAttendeeFromList"
                  >
                    {{ $t('training.presences.form.add_others') }}
                  </button>
                </div>
              </div>
            </KwaiForm>
          </div>
          <WarningComponent v-if="changed" @save="saveAttendees" />
        </div>
      </div>
    </div>
</div>
</template>

<script>
import messages from './lang';

import TrainingDayHour from './TrainingDayHour';
import MemberSummary from '@/apps/members/components/MemberSummary';

import makeForm, { makeField } from '@/js/Form';
import KwaiForm from '@/components/forms/KwaiForm';
import KwaiField from '@/components/forms/KwaiField';
import Multiselect from '@/components/forms/MultiSelect';

const WarningComponent = {
  i18n: messages,
  render(h) {
    return h('div', {
      class: {
        'uk-alert-warning': true
      },
      attrs: {
        'uk-alert': true
      }
    }, [
      h('div', {
        class: {
          'uk-grid-small': true,
          'uk-flex': true,
          'uk-flex-middle': true
        },
        attrs: {
          'uk-grid': true
        }
      }, [
        h('div', {
          class: {
            'uk-width-expand': true
          }
        },
        this.$t('training.presences.save_warning')
        ),
        h('div', {},
          [
            h('button', {
              class: {
                'uk-button': true,
                'uk-button-primary': true
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
    TrainingDayHour, MemberSummary, KwaiForm, KwaiField, Multiselect,
    WarningComponent
  },
  data() {
    return {
      presences: [],
      members: [],
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
        // TODO: For the moment only one team!
        var teamId = this.training.teams[0].id;
        this.presences = this.training.presences ?
          [ ... this.training.presences ] : [];
        if (this.training) {
          await this.$store.dispatch('team/members', {
            id: teamId
          });
          this.members = [ ... this.$store.getters['team/members'](teamId) ];
        }
      }
      this.$store.dispatch('member/browse');
    },
    removePresence(member) {
      this.presences = this.presences.filter(p => p.id !== member.id);
      this.members.push(member);
      this.changed = true;
    },
    addPresence(member) {
      this.presences.push(member);
      this.members = this.members.filter(m => m.id !== member.id);
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
