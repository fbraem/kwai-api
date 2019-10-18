<template>
  <!-- eslint-disable max-len -->
  <div class="container mx-auto mt-4"
  >
    <Spinner
      v-if="$wait.is('training.definitions.browse')"
      class="text-center"
    />
    <div v-else>
      <Alert
        v-if="noData"
        type="warning">
          {{ $t('training.definitions.no_data') }}
      </Alert>
      <table
        v-else
        class="w-full border-collapse text-left"
      >
        <tr class="bg-gray-500 border-b border-gray-200">
          <th class="py-2 px-3 font-bold uppercase text-sm text-white">
            {{ $t('name') }}
          </th>
          <th class="py-2 px-3 font-bold uppercase text-sm text-white">
            {{ $t('training.definitions.weekday') }}
          </th>
          <th class="py-2 px-3 font-bold uppercase text-sm text-white">
            {{ $t('description') }}
          </th>
          <th class="py-2 px-3 font-bold uppercase text-sm text-white">
            {{ $t('team') }}
          </th>
          <th class="py-2 px-3 font-bold uppercase text-sm text-white">
            {{ $t('season') }}
          </th>
          <th class="py-2 px-3 font-bold uppercase text-sm text-white">
            {{ $t('training.definitions.form.active.label') }}
          </th>
          <th></th>
        </tr>
        <tr
          v-for="definition in definitions"
          :key="definition.id"
          class="odd:bg-gray-200 border-b border-gray-400"
        >
          <td class="py-2 px-3 text-gray-700">
            <router-link :to="{ name: 'trainings.definitions.read', params: { id : definition.id} }">
              {{ definition.name }}
            </router-link>
          </td>
          <td class="py-2 px-3 text-gray-700">
            {{ definition.weekdayText }}
          </td>
          <td class="py-2 px-3 text-gray-700 text-sm">
            {{ definition.description }}
          </td>
          <td class="py-2 px-3 text-gray-700">
            <router-link
              v-if="definition.team"
              :to="{ name: 'teams.read', params: { id : definition.team.id} }"
            >
              {{ definition.team.name }}
            </router-link>
            <div
              v-else
              class="text-center"
            >
              <i class="fas fa-minus"></i>
            </div>
          </td>
          <td class="py-2 px-3 text-gray-700">
            <router-link
              v-if="definition.season"
              :to="{ name: 'seasons.read', params: { id : definition.season.id} }"
            >
              {{ definition.season.name }}
            </router-link>
            <div
              v-else
              class="text-center"
            >
              <i class="fas fa-minus"></i>
            </div>
          </td>
          <td class="py-2 px-3 text-gray-700 text-center">
            <i
              v-if="definition.active"
              class="fas fa-check"
            >
            </i>
            <i
              v-else
              class="fas fa-times text-red-500"
            >
            </i>
          </td>
          <td class="py-2 px-3 text-gray-700 text-right">
            <router-link
              v-if="$can('update', definition)"
              class="icon-button text-gray-700 hover:bg-gray-300"
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
import Alert from '@/components/Alert';

export default {
  components: {
    Spinner,
    Alert
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
