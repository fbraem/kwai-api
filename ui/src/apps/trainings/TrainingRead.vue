<template>
  <!-- eslint-disable max-len -->
  <div
    v-if="training"
    class="container mx-auto mt-6 w-full sm:w-1/2 lg:w-1/3"
  >
    <TrainingCard :training="training">
      <div class="border-t border-gray-300 p-6">
        <h3>
          {{ $t('title') }} &bull; {{ training.content.title }}
        </h3>
        <p>
          {{ training.content.summary }}
        </p>
        <Alert
          v-if="training.event.cancelled"
          type="danger"
        >
          {{ $t('cancelled' )}}
        </Alert>
      </div>
      <div class="border-t border-gray-300 p-6">
        <div v-if="training.coaches">
          <h4>{{ $t('coaches') }}</h4>
          <ul class="list-disc">
            <li
              v-for="(coach, index) in training.coaches"
              :key="index">
              {{ coach.name }}
            </li>
          </ul>
        </div>
        <div v-if="canManagePresences">
          <h4>{{ $t('training.presences.title') }}</h4>
          <ul class="list-disc">
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
        class="flex justify-end border-t border-gray-300 p-6"
      >
        <router-link
          :to="{ name: 'trainings.presences', params: {id: training.id} }"
          class="icon-button text-gray-700 hover:bg-gray-300"
        >
          <i class="fas fa-address-book"></i>
        </router-link>
      </div>
    </TrainingCard>
  </div>
</template>

<script>
import messages from './lang';

import Presence from '@/models/trainings/Presence';

import TrainingCard from './TrainingCard';
import Alert from '@/components/Alert';

export default {
  i18n: messages,
  components: {
    TrainingCard,
    Alert
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
