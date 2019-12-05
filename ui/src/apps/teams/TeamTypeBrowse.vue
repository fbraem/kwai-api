<template>
  <div class="flex flex-wrap justify-center">
    <Spinner v-if="$wait.is('teamtypes.browse')" />
    <Alert
      v-if="noTypes"
      type="warning"
    >
      {{ $t('no_types') }}
    </Alert>
    <div
      v-else
      class="flex flex-wrap"
    >
      <div
        v-for="type in types"
        :key="type.id"
        class="p-4 w-full md:w-1/2"
      >
        <TeamTypeCard :type="type" />
      </div>
    </div>
  </div>
</template>

<script>
import messages from './lang';

import Spinner from '@/components/Spinner';
import Alert from '@/components/Alert';
import TeamTypeCard from './TeamTypeCard';

export default {
  i18n: messages,
  components: {
    Spinner,
    TeamTypeCard,
    Alert
  },
  computed: {
    types() {
      return this.$store.state.teamType.types;
    },
    noTypes() {
      return this.types && this.types.length === 0;
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
    fetchData() {
      this.$store.dispatch('teamType/browse');
    }
  }
};
</script>
