<template>
  <!-- eslint-disable max-len -->
  <div class="uk-grid">
    <div class="uk-width-1-1@s uk-width-5-6@m">
      <h1 class="uk-h1">{{ $t('seasons') }}</h1>
      <h3 v-if="season" class="uk-h3 uk-margin-remove">
        {{ season.name }}
      </h3>
    </div>
    <div class="uk-width-1-1@s uk-width-1-6@m">
      <div class="uk-flex uk-flex-right">
        <div>
          <router-link class="uk-icon-button" :to="{ name: 'seasons.browse' }">
            <i class="fas fa-list"></i>
          </router-link>
        </div>
        <div class="uk-margin-small-left">
          <router-link v-if="season && $season.isAllowed('update', season)" class="uk-icon-button uk-link-reset" :to="{ name: 'seasons.update', params: { id:  season.id } }">
            <i class="fas fa-edit"></i>
          </router-link>
        </div>
        <div v-if="season && $season.isAllowed('remove', season)" class="uk-margin-small-left">
          <a uk-toggle="target: #delete-season" class="uk-icon-button uk-link-reset">
            <i class="fas fa-trash"></i>
          </a>
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
    season() {
      return this.$store.getters['season/season'](this.$route.params.id);
    }
  }
};
</script>
