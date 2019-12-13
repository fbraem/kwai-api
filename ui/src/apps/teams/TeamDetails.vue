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
      <Attributes :attributes="attributes">
        <template v-slot:value_season="{ attribute }">
          <router-link
            v-if="attribute.value"
            :to="{ name: 'seasons.read', params: { id : attribute.value.id} }"
          >
            {{ attribute.value.name }}
          </router-link>
        </template>
      </Attributes>
      <div class="flex justify-between border-t mb-2 sm:mb-4 pt-3">
        <div class="flex flex-wrap text-xs">
          <div class="mr-4">
            <strong>Aangemaakt:</strong> {{ team.localCreatedAt }}
          </div>
          <div>
            <strong>Laatst gewijzigd:</strong> {{ team.localUpdatedAt }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import messages from './lang';

import Spinner from '@/components/Spinner.vue';
import Alert from '@/components/Alert';
import Attributes from '@/components/Attributes';

export default {
  components: {
    Spinner,
    Alert,
    Attributes
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
    },
    attributes() {
      return {
        name: {
          label: this.$t('name'),
          value: this.team.name
        },
        season: {
          label: this.$t('season'),
          value: this.team.season
        },
        members: {
          label: this.$t('members'),
          value: this.team.members_count
        },
        remark: {
          label: this.$t('remark'),
          value: this.team.remark
        },
      };
    }
  }
};
</script>
