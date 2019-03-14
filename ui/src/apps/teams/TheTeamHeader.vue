<template>
  <!-- eslint-disable max-len -->
  <div class="uk-grid">
    <div class="uk-width-1-1@s uk-width-5-6@m">
      <h1 class="uk-h1">
        {{ $t('teams') }}
      </h1>
      <h3
        v-if="team"
        class="uk-h3 uk-margin-remove"
      >
        {{ team.name }}
      </h3>
    </div>
    <div class="uk-width-1-1@s uk-width-1-6@m">
      <div class="uk-flex uk-flex-right">
        <div>
          <router-link
            class="uk-icon-button uk-link-reset"
            :to="{ 'name' : 'teams.browse' }"
          >
            <i class="fas fa-list"></i>
          </router-link>
        </div>
        <div class="uk-margin-small-left">
          <router-link
            v-if="team && $can('update', team)"
            class="uk-icon-button uk-link-reset"
            :to="{ 'name' : 'teams.update', params : { id : team.id } }"
          >
            <i class="fas fa-edit"></i>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import messages from './lang';

export default {
  i18n: messages,
  computed: {
    team() {
      return this.$store.getters['team/team'](this.$route.params.id);
    },
  }
};
</script>
