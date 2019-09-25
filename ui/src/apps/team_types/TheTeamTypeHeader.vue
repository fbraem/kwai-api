<template>
  <div class="hero-container">
    <div>
      <h1>
        {{ $t('types') }}
      </h1>
      <h3 v-if="teamtype">
        {{ teamtype.name }}
      </h3>
    </div>
    <div
      class="kwai-buttons"
      style="display: flex; justify-content: flex-end; flex-flow: row;"
    >
      <router-link
        class="secondary:kwai-icon-button"
        :to="{ 'name' : 'team_types.browse' }"
      >
        <i class="fas fa-list"></i>
      </router-link>
      <router-link
        v-if="$can('update', teamtype)"
        class="secondary:kwai-icon-button"
        :to="{ name : 'team_types.update' }"
      >
        <i class="fas fa-edit"></i>
      </router-link>
    </div>
  </div>
</template>

<script>
import messages from './lang';

export default {
  i18n: messages,
  computed: {
    teamtype() {
      return this.$store.getters['teamType/type'](this.$route.params.id);
    }
  }
};
</script>
