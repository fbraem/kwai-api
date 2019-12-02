<template>
  <div class="container mx-auto p-4">
    <router-view name="season_information">
    </router-view>
  </div>
</template>

<script>
export default {
  computed: {
    season() {
      return this.$store.state.season.active;
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
        this.$store.dispatch('season/read', {
          id: params.id
        });
      } catch (error) {
        console.log(error);
      }
    }
  }
};
</script>
