<template>
  <!-- eslint-disable max-len -->
  <div class="bg-gray-300 p-10">
    <div
      v-if="member"
      class="container mx-auto py-3"
    >
      <ProfileCard
        class="lg:w-2/3 mx-auto bg-white"
        :image="image"
      >
        <template slot="header">
          <h1 class="m-0">
            {{ member.person.name }}
            <sup
              :class="flagClass"
              class="flag-icon text-sm"
            >
          </sup>
          </h1>
          <div class="flex flex-wrap">
            <div class="p-2 flex items-center">
              <div>
                <i class="fas fa-id-card text-gray-500"></i>
              </div>
              <div class="text-sm ml-2">
                {{ member.license }}
                (vervalt
                <span :class="licenseDateClass">{{ member.formatted_license_end_date }}</span>)
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
    </div>
  </div>
</template>

<script>
import ProfileCard from '@/components/ProfileCard';

export default {
  components: {
    ProfileCard
  },
  computed: {
    member() {
      return this.$store.getters['member/member'](this.$route.params.id);
    },
    image() {
      return this.member?.image ?? require('./images/no_avatar.png');
    },
    flagClass() {
      return 'flag-icon-' + this.member.person.nationality.iso_2.toLowerCase();
    },
    licenseDateClass() {
      return {
        'text-red-700': this.member.license_ended,
        'font-bold': this.member.license_ended
      };
    }
  },
  beforeRouteEnter(to, from, next) {
    next(async(vm) => {
      await vm.fetchData(to.params);
      next();
    });
  },
  async beforeRouteUpdate(to, from, next) {
    await this.fetchData(to.params);
    next();
  },
  methods: {
    fetchData(params) {
      try {
        this.$store.dispatch('member/read', {
          id: params.id
        });
      } catch (error) {
        console.log(error);
      }
    }
  }
};
</script>
