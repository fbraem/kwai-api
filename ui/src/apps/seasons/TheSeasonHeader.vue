<template>
  <Header
    v-if="season"
    :title="$t('seasons')"
    :subtitle="season.name"
    :toolbar="toolbar"
  >
    <AreYouSure
      v-show="isModalVisible"
      :yes="$t('delete')"
      :no="$t('cancel')"
      @sure="deleteSeason"
      @close="close"
    >
      {{ $t('are_you_sure') }}
    </AreYouSure>
  </Header>
</template>

<script>
import messages from './lang';

import AreYouSure from '@/components/AreYouSure';
import Header from '@/components/Header';

export default {
  i18n: messages,
  components: {
    AreYouSure, Header
  },
  data() {
    return {
      isModalVisible: false
    };
  },
  computed: {
    season() {
      return this.$store.getters['season/season'](this.$route.params.id);
    },
    toolbar() {
      const buttons = [];
      if (this.$can('update', this.season)) {
        buttons.push({
          icon: 'fas fa-edit',
          route: {
            name: 'seasons.update',
            params: {
              id: this.season.id
            }
          }
        });
      }
      if (this.$can('delete', this.season)) {
        buttons.push({
          icon: 'fas fa-trash',
          method: this.showModal
        });
      }
      return buttons;
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
      this.close();
      console.log('delete');
    }
  }
};
</script>
