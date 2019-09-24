<template>
  <div>
    <Header
      :title="$t('teams')"
      :subtitle="subtitle"
      :toolbar="toolbar"
    />
    <AreYouSure
      v-show="showAreYouSure"
      @close="showAreYouSure = false;"
      :yes="$t('delete')"
      :no="$t('cancel')"
      @sure="deleteTeam"
    >
      <template slot="title">
        {{ $t('delete') }}
      </template>
      {{ $t('sure_to_delete_team') }}
    </AreYouSure>
  </div>
</template>

<script>
import messages from './lang';

import Header from '@/components/Header';
import AreYouSure from '@/components/AreYouSure.vue';

export default {
  i18n: messages,
  components: {
    Header,
    AreYouSure
  },
  data() {
    return {
      showAreYouSure: false
    };
  },
  computed: {
    team() {
      return this.$store.getters['team/team'](this.$route.params.id);
    },
    subtitle() {
      return this.team ? this.team.name : null;
    },
    toolbar() {
      const buttons = [
        {
          icon: 'fas fa-list',
          route: {
            name: 'teams.browse'
          }
        },
      ];
      if (this.team) {
        if (this.$can('update', this.team)) {
          buttons.push({
            icon: 'fas fa-edit',
            route: {
              name: 'teams.update',
              params: {
                id: this.team.id
              }
            }
          });
        }
        if (this.$can('delete', this.team)) {
          buttons.push({
            icon: 'fas fa-trash',
            method: this.showModal
          });
        }
      }
      return buttons;
    }
  },
  methods: {
    showModal() {
      this.showAreYouSure = true;
    },
    deleteTeam() {
      console.log('delete');
    }
  }
};
</script>
