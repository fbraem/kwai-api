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
        class="table table-striped"
      >
        <thead>
          <tr>
            <th scope="col">
              {{ $t('name') }}
            </th>
            <th scope="col">
              {{ $t('training.definitions.weekday') }}
            </th>
            <th scope="col">
              {{ $t('description') }}
            </th>
            <th scope="col">
              {{ $t('team') }}
            </th>
            <th scope="col">
              {{ $t('season') }}
            </th>
            <th scope="col">
              {{ $t('training.definitions.form.active.label') }}
            </th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="definition in definitions"
            :key="definition.id"
          >
            <th scope="row">
              <router-link
                :to="{
                  name: 'trainings.definitions.read',
                  params: {
                    id : definition.id
                  }
                }"
              >
                {{ definition.name }}
              </router-link>
            </th>
            <td>
              {{ definition.weekdayText }}
            </td>
            <td>
              {{ definition.description }}
            </td>
            <td>
              <router-link
                v-if="definition.team"
                :to="{
                  name: 'teams.read',
                  params: {
                    id: definition.team.id
                  }
                }"
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
            <td>
              <router-link
                v-if="definition.season"
                :to="{
                  name: 'seasons.read',
                  params: {
                    id: definition.season.id
                  }
                }"
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
            <td>
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
            <td class="text-right">
              <router-link
                v-if="$can('update', definition)"
                class="icon-button text-gray-700 hover:bg-gray-300"
                :to="{
                  name: 'trainings.definitions.update',
                    params: {
                      id: definition.id
                    }
                }"
              >
                <i class="fas fa-edit"></i>
              </router-link>
            </td>
          </tr>
        </tbody>
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
      return this.$store.state.training.definition.all;
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
