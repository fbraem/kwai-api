<template>
  <div>
    <Spinner v-if="$wait.is('training.coaches.read')" />
    <CoachCard v-if="coach" :coach="coach" />
    <div
      v-if="notAllowed"
      class="uk-alert-danger"
      uk-alert
    >
        {{ $t('not_allowed') }}
    </div>
    <div
      v-if="notFound"
      class="uk-alert-danger"
      uk-alert
    >
        {{ $t('training.coaches.not_found') }}
    </div>
    <div class="uk-width-1-1">
      <router-view name="coach_information" />
    </div>
  </div>
</template>

<script>
import messages from './lang';

import CoachCard from './components/CoachCard';

import Spinner from '@/components/Spinner';

export default {
  components: {
    Spinner, CoachCard
  },
  i18n: messages,
  computed: {
    coach() {
      return this.$store.getters['training/coach/coach'](
        this.$route.params.id
      );
    },
    error() {
      return this.$store.state.training.coach.error;
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
      this.$store.dispatch('training/coach/read', {
        id: id
      }).catch((err) => {
        console.log(err);
      });
    }
  }
};
</script>
