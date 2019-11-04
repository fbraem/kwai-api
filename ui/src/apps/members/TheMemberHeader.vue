<template>
  <!-- eslint-disable max-len -->
  <div class="bg-gray-800 text-white border-b-2 border-gray-300">
    <div
      v-if="member"
      class="container mx-auto flex flex-col p-4 lg:p-6 "
    >
      <div class="text-xl font-bold flex flex-wrap justify-between">
        <div>
          <h1>Ledeninformatie</h1>
          <h2>
            {{ member.person.name }}
            <sup
              :class="flagClass"
              class="flag-icon text-sm"
            >
            </sup>
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
            class="w-full sm:w-1/2 lg:w-1/4 border-gray-600 border-b sm:border-r lg:border-b-0"
            :to="{ name: 'members.read', params: { id: member.id }}"
            linkTitle='Toon het profiel ...'
          >
            <div class="flex justify-center align-center mb-2">
              <img
                class="h-32 w-32 rounded-full"
                :src="image"
              >
            </div>
          </MegaMenuBlock>
          <MegaMenuBlock
            class="w-full sm:w-1/2 lg:w-1/4 border-gray-600 border-b sm:border-r-0 lg:border-r lg:border-b-0"
            title="Teams"
            logo="fas fa-users"
            :to="{ name: 'members.teams', params: { id: member.id }}"
          >
            <p class="text-gray-100 text-sm">
              Bekijk tot welke teams <strong>{{ member.person.name }}</strong> behoort.
            </p>
          </MegaMenuBlock>
          <MegaMenuBlock
            class="w-full sm:w-1/2 lg:w-1/4 border-gray-600 border-b sm:border-b-0 sm:border-r md:border-b-0"
            title="Trainingen"
            logo="fas fa-university"
            :to="{ name: 'members.read', params: { id: member.id }}"
          >
            <p class="text-gray-100 text-sm">
              Aan welke trainingen heeft <strong>{{ member.person.name }}</strong> deel genomen?
            </p>
          </MegaMenuBlock>
          <MegaMenuBlock
            class="w-full sm:w-1/2 lg:w-1/4"
            title="Tornooien"
            logo="fas fa-trophy"
            :to="{ name: 'members.read', params: { id: member.id }}"
          >
            <p class="text-gray-100 text-sm">
              Voor welke tornooien is <strong>{{ member.person.name }}</strong> ingeschreven?
              Wat waren de resultaten?
            </p>
          </MegaMenuBlock>
        </MegaMenu>
      </div>
    </div>
  </div>
</template>

<style>
/*
.toggleable > label:after {
    content: "\25BC";
    font-size: 10px;
    padding-left: 6px;
    position: relative;
    top: -1px;
  }
*/
  .toggle-input {
    display: none;
  }
  .toggle-input:not(checked) ~ .mega-menu {
    display: none;
  }

  .toggle-input:checked ~ .mega-menu {
    display: block;
  }

/*
  .toggle-input:checked + label {
    color: white;
  }
  .toggle-input:checked ~ label:after {
    content: "\25B2";
    font-size: 10px;
    padding-left: 6px;
    position: relative;
    top: -1px;
  }
  */

</style>

<script>
import messages from './lang';

import MegaMenu from '@/components/MegaMenu';
import MegaMenuBlock from '@/components/MegaMenuBlock';

export default {
  components: {
    MegaMenu,
    MegaMenuBlock
  },
  i18n: messages,
  data() {
    return {
      showMenu: false
    };
  },
  computed: {
    member() {
      return this.$store.getters['member/member'](this.$route.params.id);
    },
    logo() {
      if (this.member.person.isMale) {
        return 'fas fa-male';
      }
      return 'fas fa-female';
    },
    image() {
      return this.member?.image ?? require('./images/no_avatar.png');
    },
    flagClass() {
      return 'flag-icon-' + this.member.person.nationality.iso_2.toLowerCase();
    }
  },
};
</script>
