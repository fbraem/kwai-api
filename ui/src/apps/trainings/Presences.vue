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
                {{ member.person.name }}
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
                {{ member.person.name }}
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
        </div>
      </div>
    </div>
</div>
</template>

<script>
import messages from './lang';

import TrainingDayHour from './TrainingDayHour';

export default {
  i18n: messages,
  components: {
    TrainingDayHour
  },
  data() {
    return {
      presences: [],
      members: []
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
        id: id
      }).catch((err) => {
        console.log(err);
      });
      if (this.training) {
        // TODO: For the moment only one team!
        var teamId = this.training.teams[0].id;
        this.presences = [ ... this.training.presences ];
        if (this.training) {
          await this.$store.dispatch('team/members', {
            id: teamId
          });
          this.members = [ ... this.$store.getters['team/members'](teamId) ];
        }
      }
    },
    removePresence(member) {
      this.presences = this.presences.filter((p) => p.id !== member.id);
      this.members.push(member);
    },
    addPresence(member) {
      this.presences.push(member);
      this.members = this.members.filter((m) => m.id !== member.id);
    }
  }
};
</script>
