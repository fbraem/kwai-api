<template>
  <div>
    <Spinner v-if="$wait.is('teamtypes.browse')" />
    <div
      v-else
      class="uk-child-width-1-1"
      uk-grid
    >
      <div
        v-if="noTypes"
        class="uk-alert uk-alert-warning"
      >
        {{ $t('no_types') }}
      </div>
      <div v-else>
        <table class="uk-table uk-table-striped">
          <tr>
            <th>{{ $t('name') }}</th>
            <th class="uk-table-shrink"></th>
          </tr>
          <tr
            v-for="type in types"
            :key="type.id"
          >
            <td>
              <router-link
                :to="{ name: 'team_types.read', params: { id : type.id} }"
              >
                {{ type.name }}
              </router-link>
            </td>
            <td>
              <router-link
                v-if="$can('update', type)"
                class="uk-icon-button"
                style="margin-top:-10px"
                :to="{ name : 'team_types.update', params : { id : type.id } }"
              >
                <i class="fas fa-edit"></i>
              </router-link>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import messages from './lang';

import Spinner from '@/components/Spinner';

export default {
  i18n: messages,
  components: {
    Spinner
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
