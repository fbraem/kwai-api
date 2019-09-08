<template>
  <!-- eslint-disable max-len -->
  <div class="page-container">
    <div style="grid-column: 1 / span 2;">
      <Spinner v-if="$wait.is('teamtypes.browse')" />
      <div
        v-if="noTypes"
        class="kwai-alert kwai-theme-warning"
      >
        {{ $t('no_types') }}
      </div>
      <div v-else>
        <ul class="kwai-list" style="display: flex; flex-wrap: wrap;">
          <li
            class="teamtype-item"
            v-for="type in types"
            :key="type.id"
          >
            <TeamTypeCard :type="type" />
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<style lang=scss>
  @import "@/site/scss/_mq.scss";

  .teamtype-item {
    margin-top: 0px !important;
    padding-bottom: 20px;
    padding-left: 20px;

    @include mq($until: tablet) {
      width: 100%;
    }
    @include mq($from: tablet) {
      width: 50%;
    }
  }
</style>

<script>
import messages from './lang';

import Spinner from '@/components/Spinner';
import TeamTypeCard from './TeamTypeCard';

export default {
  i18n: messages,
  components: {
    Spinner,
    TeamTypeCard
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
