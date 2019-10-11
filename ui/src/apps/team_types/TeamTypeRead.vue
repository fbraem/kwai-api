<template>
  <!-- eslint-disable max-len -->
  <div class="container mt-4 mx-auto">
    <Spinner v-if="$wait.is('teamtypes.read')" />
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
      {{ $t('not_found') }}
    </Alert>
    <div
      v-if="teamtype"
    >
      <TeamTypeCard :type="teamtype" />
    </div>
  </div>
</template>

<script>
import messages from './lang';

import Spinner from '@/components/Spinner';
import Alert from '@/components/Alert';
import TeamTypeCard from './TeamTypeCard';

export default {
  i18n: messages,
  components: {
    Spinner, Alert, TeamTypeCard
  },
  computed: {
    teamtype() {
      return this.$store.getters['teamType/type'](this.$route.params.id);
    },
    gender() {
      var gender = this.teamtype.gender;
      if (gender === 0) {
        return this.$t('no_restriction');
      } else if (gender === 1) {
        return this.$t('male');
      } else {
        return this.$t('female');
      }
    },
    error() {
      return this.$store.state.teamType.error;
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
      await vm.fetchData(to.params);
      next();
    });
  },
  async beforeRouteUpdate(to, from, next) {
    await this.fetchData(to.params);
    next();
  },
  methods: {
    fetchData(params) {
      this.$store.dispatch('teamType/read', { id: params.id })
        .catch((error) => {
          console.log(error);
        });
    }
  }
};
</script>
