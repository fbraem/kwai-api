<template>
  <ProfileCard
    class="lg:w-2/3 mx-auto bg-white"
    :image="image"
  >
    <template slot="header">
      <h1 class="m-0 text-center sm:text-left">
        <i
          v-if="member.person.isMale"
          class="fas fa-male"
        >
        </i>
        <i
          v-if="member.person.isFemale"
          class="fas fa-female"
        >
        </i>
        {{ member.person.name }}
        <sup
          :class="flagClass"
          class="flag-icon text-sm"
        >
        </sup>
      </h1>
      <div v-if="!member.active" class="ribbon">
        <span>Niet actief</span>
      </div>
      <div class="flex flex-wrap">
        <div class="p-2 flex items-center">
          <div>
            <i class="fas fa-id-card text-gray-500"></i>
          </div>
          <div class="text-sm ml-2">
            {{ member.license }}
            (vervalt
            <span :class="licenseDateClass">
              {{ member.formatted_license_end_date }}</span>)
          </div>
        </div>
        <div class="p-2 flex items-center">
          <div>
            <i class="fas fa-birthday-cake text-gray-500"></i>
          </div>
          <div class="text-sm ml-2">
            {{ member.person.formatted_birthdate }} ({{ member.person.age }} jr)
          </div>
        </div>
      </div>
    </template>
  </ProfileCard>
</template>

<style>
.ribbon {
  position: absolute;
  right: -5px; top: -5px;
  z-index: 1;
  overflow: hidden;
  width: 75px; height: 75px;
  text-align: right;
}
.ribbon span {
  font-size: 10px;
  font-weight: bold;
  text-transform: uppercase;
  text-align: center;
  line-height: 20px;
  transform: rotate(45deg);
  -webkit-transform: rotate(45deg);
  width: 100px;
  @apply block absolute bg-red-700 text-white;
  box-shadow: 0 3px 10px -5px rgba(0, 0, 0, 1);
  top: 19px; right: -21px;
}
.ribbon span::before {
  content: "";
  position: absolute; left: 0px; top: 100%;
  z-index: -1;
  border-left: 3px solid #8F0808;
  border-right: 3px solid transparent;
  border-bottom: 3px solid transparent;
  border-top: 3px solid #8F0808;
}
.ribbon span::after {
  content: "";
  position: absolute; right: 0px; top: 100%;
  z-index: -1;
  border-left: 3px solid transparent;
  border-right: 3px solid #8F0808;
  border-bottom: 3px solid transparent;
  border-top: 3px solid #8F0808;
}
</style>

<script>
import Member from '@/models/Member';

import ProfileCard from '@/components/ProfileCard';

export default {
  props: {
    member: {
      type: Member
    }
  },
  components: {
    ProfileCard
  },
  computed: {
    image() {
      return this.member?.image ?? require('../images/no_avatar.png');
    },
    flagClass() {
      return 'flag-icon-' + this.member.person.nationality.iso_2.toLowerCase();
    },
    licenseDateClass() {
      return {
        'text-red-700': this.member.license_ended,
        'font-bold': this.member.license_ended
      };
    },
    logo() {
      if (this.member.person.isMale) {
        return 'fas fa-male';
      }
      return 'fas fa-female';
    }
  }
};
</script>
