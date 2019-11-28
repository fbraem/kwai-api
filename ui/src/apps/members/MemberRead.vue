<template>
  <div
    v-if="member"
    class="flex flex-col"
  >
    <div class="bg-gray-300 p-10">
      <MemberProfileCard :member="member" />
    </div>
    <router-view name="member_information">
    </router-view>
  </div>
</template>

<script>
import MemberProfileCard from './components/MemberProfileCard';

export default {
  components: {
    MemberProfileCard
  },
  computed: {
    member() {
      return this.$store.state.member.active;
    },
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
