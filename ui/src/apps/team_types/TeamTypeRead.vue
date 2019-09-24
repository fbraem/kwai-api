<template>
  <!-- eslint-disable max-len -->
  <div class="page-container">
    <Spinner v-if="$wait.is('teamtypes.read')" />
    <div
      v-if="notAllowed"
      class="kwai-alert kwai-theme-danger"
    >
      {{ $t('not_allowed') }}
    </div>
    <div
      v-if="notFound"
      class="kwai-alert kwai-theme-danger"
    >
      {{ $t('not_found') }}
    </div>
    <div v-if="teamtype">
      <div>
        <dl class="kwai-attributes">
          <dt>{{ $t('name') }}</dt>
          <dd>{{ teamtype.name }}</dd>
          <dt>{{ $t('form.team_type.start_age.label') }}</dt>
          <dd>{{ teamtype.start_age }}</dd>
          <dt>{{ $t('form.team_type.end_age.label') }}</dt>
          <dd>{{ teamtype.end_age }}</dd>
          <dt>{{ $t('form.team_type.gender.label') }}</dt>
          <dd>{{ gender }}</dd>
          <dt>{{ $t('active') }}</dt>
          <dd>
              <i
                v-if="teamtype.active"
                class="fas fa-check"
              >
              </i>
              <i
                v-else
                class="fas fa-times kwai-theme-danger"
                name="times"
              >
              </i>
          </dd>
          <dt>{{ $t('competition_label') }}</dt>
          <dd>
              <i
                v-if="teamtype.competition"
                class="fas fa-check"
              >
              </i>
              <i
                v-else
                class="fas fa-times kwai-theme-danger"
              >
              </i>
          </dd>
          <dt>{{ $t('form.team_type.remark.label') }}</dt>
          <dd>{{ teamtype.remark }}</dd>
        </dl>
      </div>
      <Spinner v-if="$wait.is('teams.read')" />
    </div>
  </div>
</template>

<script>
import messages from './lang';

import Spinner from '@/components/Spinner';

export default {
  i18n: messages,
  components: {
    Spinner
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
