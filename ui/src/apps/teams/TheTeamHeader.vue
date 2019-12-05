<template>
  <!-- eslint-disable max-len -->
  <div class="bg-gray-800 text-white border-b-2 border-gray-300">
    <div
      v-if="team"
      class="container mx-auto flex flex-col p-4 lg:p-6 "
    >
      <div class="text-xl font-bold flex flex-wrap justify-between">
        <div>
          <h1>
            <router-link
              :to="{ name: 'teams.browse' }"
              class="text-white"
            >
              {{ $t('teams') }}
            </router-link>
          </h1>
          <h2>{{ team.name }}</h2>
        </div>
        <div>
          <i
            class="fas fa-bars fa-2x hover:cursor-pointer"
            @click.stop.prevent="showMenu = !showMenu"
          >
          </i>
        </div>
      </div>
      <div v-if="showMenu">
        <MegaMenu
          class="mega-menu"
          role="toggle"
        >
          <MegaMenuBlock
            class="px-4 w-full sm:w-1/2 lg:w-1/4 border-gray-600 border-b sm:border-r lg:border-b-0 pb-6 pt-6 lg:pt-3"
            :to="{ name: 'teams.read', params: { id: team.id }}"
            linkTitle='Toon het team ...'
          >
            <div class="flex justify-center">
              <div class="bg-gray-300 p-3 border-gray-600 border-2">
                <img
                  class="h-32 w-48"
                  :src="require('./images/team.png')"
                />
              </div>
            </div>
          </MegaMenuBlock>
          <MegaMenuBlock
            class="px-4 w-full sm:w-1/2 lg:w-1/4 border-gray-600 border-b sm:border-r-0 lg:border-r lg:border-b-0 pb-6 pt-6 lg:pt-3"
            :to="{ name: 'team.members', params: { id: team.id }}"
            logo="fas fa-users"
            :title="$t('members')"
            linkTitle="Toon de leden"
          >
            <p class="text-gray-100 text-sm">
              Wie zijn de leden van dit team?
            </p>
          </MegaMenuBlock>
          <MegaMenuBlock
            class="px-4 w-full sm:w-1/2 lg:w-1/4 border-gray-600 border-b sm:border-b-0 sm:border-r md:border-b-0 pb-6 pt-6 lg:pt-3"
            title="Tornooien"
            logo="fas fa-trophy"
            :to="{ name: 'team.tournaments', params: { id: team.id }}"
          >
            <p class="text-gray-100 text-sm">
              Welke tornooien zijn er voor dit team georganiseerd?
            </p>
          </MegaMenuBlock>
          <MegaMenuBlock
            class="px-4 w-full sm:w-1/2 lg:w-1/4 border-gray-600 pb-6 pt-6 lg:pt-3"
            title="Trainingen"
            logo="fas fa-university"
            :to="{ name: 'team.trainings', params: { id: team.id }}"
          >
            <p class="text-gray-100 text-sm">
              Welke trainingen zijn er voor dit team?
            </p>
          </MegaMenuBlock>
        </MegaMenu>
      </div>
    </div>
  </div>
<!--
  <Header
    :title="$t('teams')"
    :subtitle="subtitle"
    :toolbar="toolbar"
  >
    <AreYouSure
      :show="showAreYouSure"
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
  </Header>
-->
</template>

<script>
import messages from './lang';

// import Header from '@/components/Header';
// import AreYouSure from '@/components/AreYouSure.vue';

import MegaMenu from '@/components/MegaMenu';
import MegaMenuBlock from '@/components/MegaMenuBlock';

export default {
  i18n: messages,
  components: {
/*
    Header,
    AreYouSure,
*/
    MegaMenu,
    MegaMenuBlock
  },
  data() {
    return {
      showAreYouSure: false,
      showMenu: false
    };
  },
  computed: {
    team() {
      return this.$store.state.team.active;
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
