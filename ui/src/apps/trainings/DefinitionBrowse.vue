<template>
  <!-- eslint-disable max-len -->
  <div>
    <Spinner v-if="$wait.is('training.definitions.browse')" />
    <div
      v-else
      uk-grid
    >
      <div
        v-if="noData"
        class="uk-width-1-1"
      >
        <div class="uk-alert-warning" uk-alert>
          {{ $t('training.definitions.no_data') }}
        </div>
      </div>
      <div v-else>
        <table class="uk-table uk-table-small uk-table-divider uk-table-middle">
          <tr>
            <th>
              {{ $t('name') }}
            </th>
            <th>
              {{ $t('training.definitions.weekday') }}
            </th>
            <th>
              {{ $t('description') }}
            </th>
            <th>
              {{ $t('team') }}
            </th>
            <th>
              {{ $t('season') }}
            </th>
            <th>
              {{ $t('training.definitions.form.active.label') }}
            </th>
            <th class="uk-table-shrink"></th>
          </tr>
          <tr
            v-for="definition in definitions"
            :key="definition.id"
          >
            <td>
              <router-link :to="{ name: 'trainings.definitions.read', params: { id : definition.id} }">
                {{ definition.name }}
              </router-link>
            </td>
            <td>
              {{ definition.weekdayText }}
            </td>
            <td class="uk-text-meta">
              {{ definition.description }}
            </td>
            <td>
              <router-link
                v-if="definition.team"
                :to="{ name: 'teams.read', params: { id : definition.team.id} }"
              >
                {{ definition.team.name }}
              </router-link>
              <div
                v-else
                class="uk-text-center"
              >
                <i class="fas fa-minus"></i>
              </div>
            </td>
            <td>
              <router-link
                v-if="definition.season"
                :to="{ name: 'seasons.read', params: { id : definition.season.id} }"
              >
                {{ definition.season.name }}
              </router-link>
              <div
                v-else
                class="uk-text-center"
              >
                <i class="fas fa-minus"></i>
              </div>
            </td>
            <td class="uk-text-center">
              <i
                v-if="definition.active"
                class="fas fa-check"
              >
              </i>
              <i
                v-else
                class="fas fa-times uk-text-danger"
              >
              </i>
            </td>
            <td>
              <router-link
                v-if="$can('update', definition)"
                class="uk-icon-button uk-link-reset"
                :to="{ name : 'trainings.definitions.update', params : { id : definition.id } }"
              >
                <i class="fas fa-edit uk-text-muted"></i>
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
  components: {
    Spinner
  },
  i18n: messages,
  computed: {
    definitions() {
      return this.$store.state.training.definition.definitions;
    },
    noData() {
      return this.definitions && this.definitions.length === 0;
    }
  },
  beforeRouteEnter(to, from, next) {
    next(async(vm) => {
      await vm.fetchData();
      next();
    });
  },
  async beforeRouteUpdate(to, from, next) {
    await this.fetchData();
    next();
  },
  methods: {
    fetchData() {
      this.$store.dispatch('training/definition/browse');
    }
  }
};
</script>
