<template>
  <div class="hero-container">
    <div>
      <h1>
        {{ $t('seasons') }}
      </h1>
      <h3 v-if="season">
        {{ season.name }}
      </h3>
    </div>
    <div
      style="display: flex; justify-content: flex-end; flex-flow: row"
      class="kwai-buttons"
    >
      <router-link
        class="kwai-icon-button kwai-theme-muted"
        :to="{ name: 'seasons.browse' }"
      >
        <i class="fas fa-list"></i>
      </router-link>
      <router-link
        v-if="season && $can('update', season)"
        class="kwai-icon-button kwai-theme-muted"
        :to="{ name: 'seasons.update', params: { id:  season.id } }"
      >
        <i class="fas fa-edit"></i>
      </router-link>
      <a v-if="$can('delete', season)"
        @click.prevent.stop="showModal"
        class="kwai-icon-button kwai-theme-muted"
      >
        <i class="fas fa-trash"></i>
      </a>
    </div>
    <AreYouSure
      v-show="isModalVisible"
      :yes="$t('delete')"
      :no="$t('cancel')"
      @sure="deleteSeason"
      @close="close"
    >
      {{ $t('are_you_sure') }}
    </AreYouSure>
  </div>
</template>

<script>
import messages from './lang';

import AreYouSure from '@/components/AreYouSure.vue';

export default {
  i18n: messages,
  components: {
    AreYouSure
  },
  data() {
    return {
      isModalVisible: false
    };
  },
  computed: {
    season() {
      return this.$store.getters['season/season'](this.$route.params.id);
    }
  },
  methods: {
    showModal() {
      this.isModalVisible = true;
    },
    close() {
      this.isModalVisible = false;
    },
    deleteSeason() {
      console.log('delete');
    }
  }
};
</script>
