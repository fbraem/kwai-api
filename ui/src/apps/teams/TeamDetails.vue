<template>
  <div>
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
    <Spinner v-if="$wait.is('teams.read')" />
    <div
      v-else-if="team"
      class="p-2"
    >
      <dl>
        <dt>{{ $t('name') }}</dt>
        <dd>{{ team.name }}</dd>
        <dt>{{ $t('remark') }}</dt>
        <dd>{{ team.remark }}</dd>
      </dl>
      <div class="text-xs flex flex-wrap items-baseline mt-3">
        <div class="mx-2">
          <strong>Aangemaakt:</strong> {{ team.localCreatedAt }}
        </div>
        <div class="mx-2">
          <strong>Laatst gewijzigd:</strong> {{ team.localUpdatedAt }}
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
dl {
  display: grid;
  grid-template: auto / 10em 1fr;
  @apply border-b;
}

dt {
  @apply font-bold;
  grid-column: 1;
}

dd {
  grid-column: 2;
}

dt, dd {
  @apply m-0 px-1 py-2 border-t;
}
</style>

<script>
import messages from './lang';

import Spinner from '@/components/Spinner.vue';
import Alert from '@/components/Alert';

export default {
  components: {
    Spinner,
    Alert,
  },
  i18n: messages,
  computed: {
    team() {
      return this.$store.state.team.active;
    },
    error() {
      return this.$store.state.team.error;
    },
    notAllowed() {
      return this.error && this.error.response.status === 401;
    },
    notFound() {
      return this.error && this.error.response.status === 404;
    }
  }
};
</script>
