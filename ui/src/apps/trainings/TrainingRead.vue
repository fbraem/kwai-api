<template>
  <!-- eslint-disable max-len -->
  <div
    v-if="training"
    class="page-container"
    style="display: flex; justify-content: center"
  >
    <TrainingCard :training="training">
      <div class="training-area">
        <h3>
          {{ $t('title') }} &bull; {{ training.content.title }}
        </h3>
        <p>
          {{ training.content.summary }}
        </p>
        <p
          v-if="training.event.cancelled"
          class="kwai-alert kwai-theme-danger"
        >
          {{ $t('cancelled' )}}
        </p>
      </div>
      <div class="training-area">
        <div v-if="training.coaches">
          <h4>{{ $t('coaches') }}</h4>
          <ul>
            <li
              v-for="(coach, index) in training.coaches"
              :key="index">
              {{ coach.name }}
            </li>
          </ul>
        </div>
        <div v-if="canManagePresences">
          <h4>{{ $t('training.presences.title') }}</h4>
          <ul>
            <li
              v-for="(member, index) in training.presences"
              :key="index">
              {{ member.person.name }}
            </li>
          </ul>
        </div>
      </div>
      <div
        v-if="canManagePresences"
        style="display: flex; justify-content: flex-end;"
        class="training-area"
      >
        <router-link
          :to="{ name: 'trainings.presences', params: {id: training.id} }"
          class="kwai-icon-button"
        >
          <i class="fas fa-address-book"></i>
        </router-link>
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

import Presence from '@/models/trainings/Presence';
import TrainingCard from './TrainingCard';

export default {
  i18n: messages,
  components: {
    TrainingCard
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
    },
    canManagePresences() {
      return this.$can('manage', Presence.type());
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
        id,
        cache: false
      }).catch((err) => {
        console.log(err);
      });
    },
  }
};
</script>
