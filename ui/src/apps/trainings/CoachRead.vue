<template>
  <div class="container mt-4 mx-auto">
    <Spinner
      class="align-middle"
      v-if="$wait.is('training.coaches.read')"
    />
    <div class="w-full lg:w-2/3 mx-auto p-4">
      <CoachCard
        v-if="coach"
        :coach="coach"
      />
    </div>
    <Alert
      v-if="notAllowed"
      type="danger"
    >
        {{ $t('not_allowed') }}
    </Alert>
    <Alert
      v-if="notFound"
      type="danger"
    >
        {{ $t('training.coaches.not_found') }}
    </Alert>
    <div class="mt-4">
      <router-view name="coach_information" />
    </div>
  </div>
</template>

<script>
import messages from './lang';

import CoachCard from './components/CoachCard';

import Spinner from '@/components/Spinner';
import Alert from '@/components/Alert';

export default {
  components: {
    Spinner, CoachCard, Alert
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
