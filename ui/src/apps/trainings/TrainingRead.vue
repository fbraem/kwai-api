<template>
  <!-- eslint-disable max-len -->
  <div
    v-if="training"
    class="uk-flex-center"
    uk-grid
  >
    <div class="uk-width-1-2@s">
      <div class="uk-card uk-card-default">
        <div class="uk-card-header uk-padding-remove">
          <TrainingDayHour :training="training" />
        </div>
        <div class="uk-card-body">
          <h3 class="uk-card-title">
            {{ $t('title') }} &bull; {{ training.content.title }}
          </h3>
          <p>
            {{ training.content.summary }}
          </p>
          <p
            v-if="training.event.cancelled"
            class="uk-alert-danger"
            uk-alert
          >
            {{ $t('cancelled' )}}
          </p>
        </div>
        <div class="uk-card-footer">
          <div uk-grid>
            <div
              v-if="training.coaches"
              class="uk-width-1-1"
            >
              <strong>{{ $t('coaches') }}:</strong>
              <ul class="uk-list uk-list-bullet">
                <li
                  v-for="(coach, index) in training.coaches"
                  :key="index">
                  {{ coach.name }}
                </li>
              </ul>
            </div>
            <div class="uk-width-1-1">
              <strong>{{ $t('training.presences.title') }}:</strong>
              <ul class="uk-list uk-list-bullet">
                <li
                  v-for="(member, index) in training.presences"
                  :key="index">
                  {{ member.person.name }}
                </li>
              </ul>
              <div>
                <router-link
                  :to="{ name: 'trainings.presences', params: {id: training.id} }"
                  class="uk-icon-button uk-link-reset"
                >
                  <i class="fas fa-address-book"></i>
                </router-link>
              </div>
            </div>
          </div>
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
  computed: {
    training() {
      return this.$store.getters['training/training'](
        this.$route.params.id
      );
    },
    day() {
      return this.training.event.start_date.date();
    },
    dayName() {
      return this.training.event.start_date.format('dddd');
    },
    month() {
      return this.training.event.start_date.format('MMMM');
    },
    error() {
      return this.$store.state.training.error;
    },
    notAllowed() {
      return this.error && this.error.response.status === 401;
    },
    notFound() {
      return this.error && this.error.response.status === 404;
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
    fetchData(id) {
      this.$store.dispatch('training/read', {
        id: id
      }).catch((err) => {
        console.log(err);
      });
    },
  }
};
</script>
