<template>
  <!-- eslint-disable max-len -->
  <div class="uk-grid">
    <div class="uk-width-5-6">
      <h1>{{ $t('types') }}</h1>
      <h3 v-if="teamtype" class="uk-h3 uk-margin-remove">
        {{ teamtype.name }}
      </h3>
    </div>
    <div class="uk-width-1-6">
      <div class="uk-flex uk-flex-right">
        <div>
          <router-link class="uk-icon-button uk-link-reset" :to="{ 'name' : 'team_types.browse' }">
            <i class="fas fa-list"></i>
          </router-link>
        </div>
        <div class="uk-margin-small-left">
          <router-link v-if="$team_type.isAllowed('update',teamtype)" class="uk-icon-button uk-link-reset" :to="{ name : 'team_types.update' }">
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
    teamtype() {
      return this.$store.getters['teamType/type'](this.$route.params.id);
    }
  }
};
</script>
