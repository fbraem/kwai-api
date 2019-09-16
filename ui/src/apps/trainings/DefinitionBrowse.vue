<template>
  <!-- eslint-disable max-len -->
  <div
    class="page-container"
  >
    <Spinner v-if="$wait.is('training.definitions.browse')" />
    <div
      v-else
      style="grid-column: span 2;"
    >
      <div
        v-if="noData"
        class="kwai-alert-warning kwai-theme-warning">
          {{ $t('training.definitions.no_data') }}
      </div>
      <table
        v-else
        class="kwai-table kwai-table-small kwai-table-divider kwai-table-middle"
        style="width: 100%;"
      >
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
          <th class="kwai-table-shrink"></th>
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
          <td class="kwai-text-meta">
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
            >
              <i class="fas fa-minus"></i>
            </div>
          </td>
          <td>
            <i
              v-if="definition.active"
              class="fas fa-check"
            >
            </i>
            <i
              v-else
              class="fas fa-times kwai-theme-danger"
            >
            </i>
          </td>
          <td>
            <router-link
              v-if="$can('update', definition)"
              class="kwai-icon-button"
              :to="{ name : 'trainings.definitions.update', params : { id : definition.id } }"
            >
              <i class="fas fa-edit"></i>
            </router-link>
          </td>
        </tr>
      </table>
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
