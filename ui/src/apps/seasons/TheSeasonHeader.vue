<template>
<!-- eslint-disable max-len -->
  <div class="bg-gray-800 text-white border-b-2 border-gray-300">
    <div
      v-if="season"
      class="container mx-auto flex flex-col p-4 lg:p-6 "
    >
      <div class="text-xl font-bold flex flex-wrap justify-between">
        <div>
          <h1>{{ $t('seasons') }}</h1>
          <h2>
            {{ season.name }}
          </h2>
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
            :to="{ name: 'seasons.read', params: { id: season.id }}"
            linkTitle='Toon het seizoen ...'
          >
            <div class="flex justify-center">
              <div class="h-24 w-24 text-center align-middle rounded-full flex items-center justify-center bg-gray-700 text-2xl">
                {{ season.name }}
              </div>
            </div>
          </MegaMenuBlock>
          <MegaMenuBlock
            class="px-4 w-full sm:w-1/2 lg:w-1/4 border-gray-600 border-b sm:border-r-0 lg:border-r lg:border-b-0 pb-6 pt-6 lg:pt-3"
            :to="{ name: 'seasons.teams', params: { id: season.id }}"
            logo="fas fa-users"
            :title="$t('teams')"
            linkTitle='Toon de teams ...'
          >
            <div class="flex justify-center align-center mb-2 rounded-full">
              <p class="text-gray-100 text-sm">
                Welke teams zijn er gekoppeld aan dit seizoen?
              </p>
            </div>
          </MegaMenuBlock>
          <MegaMenuBlock
            class="px-4 w-full sm:w-1/2 lg:w-1/4 border-gray-600 border-b sm:border-b-0 sm:border-r md:border-b-0 pb-6 pt-6 lg:pt-3"
            title="Trainingsmomenten"
            logo="fas fa-calendar-alt"
            :to="{ name: 'seasons.definitions', params: { id: season.id }}"
          >
            <p class="text-gray-100 text-sm">
              Zijn er trainingsmomenten gekoppeld aan dit seizoen?
            </p>
          </MegaMenuBlock>
          <MegaMenuBlock
            class="px-4 w-full sm:w-1/2 lg:w-1/4 border-gray-600 pb-6 pt-6 lg:pt-3"
            title="Trainingen"
            logo="fas fa-university"
            :to="{ name: 'seasons.trainings', params: { id: season.id }}"
          >
            <p class="text-gray-100 text-sm">
              Welke trainingen zijn er gekoppeld aan dit seizoen?
            </p>
          </MegaMenuBlock>
        </MegaMenu>
      </div>
    </div>
  </div>
<!--
  <Header
    v-if="season"
    :title="$t('seasons')"
    :subtitle="season.name"
    :toolbar="toolbar"
  >
    <AreYouSure
      :show="isModalVisible"
      :yes="$t('delete')"
      :no="$t('cancel')"
      @sure="deleteSeason"
      @close="close"
    >
      {{ $t('are_you_sure') }}
    </AreYouSure>
  </Header>
-->
</template>

<style scoped>
  .toggle-input {
    display: none;
  }
  .toggle-input:not(checked) ~ .mega-menu {
    display: none;
  }

  .toggle-input:checked ~ .mega-menu {
    display: block;
  }
</style>

<script>
import messages from './lang';

import AreYouSure from '@/components/AreYouSure';
import Header from '@/components/Header';
import MegaMenu from '@/components/MegaMenu';
import MegaMenuBlock from '@/components/MegaMenuBlock';

export default {
  i18n: messages,
  components: {
    AreYouSure, Header, MegaMenu, MegaMenuBlock
  },
  data() {
    return {
      isModalVisible: false,
      showMenu: false
    };
  },
  computed: {
    season() {
      return this.$store.state.season.active;
    },
    toolbar() {
      const buttons = [{
        icon: 'fas fa-list',
        route: {
          name: 'seasons.browse'
        },
      }];
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
