<template>
  <!-- eslint-disable max-len -->
  <div class="container mt-4 mx-auto">
    <Spinner v-if="$wait.is('training.coaches.browse')" />
    <div v-else>
      <Alert
        v-if="noData"
        type="warning"
      >
        {{ $t('training.coaches.no_data') }}
      </Alert>
      <table
        v-else
        class="w-full border-collapse text-left"
      >
        <tr class="bg-gray-500 border-b border-gray-200">
          <th class="py-2 px-3 font-bold uppercase text-white">
            {{ $t('name') }}
          </th>
          <th class="py-2 px-3 font-bold uppercase text-white">
            {{ $t('training.coaches.form.diploma.label') }}
          </th>
          <th class="py-2 px-3 font-bold uppercase text-white text-center">
            {{ $t('training.coaches.form.active.label') }}
          </th>
          <th class="py-2 px-3 font-bold uppercase text-white">
          </th>
        </tr>
        <tr
          v-for="coach in coaches"
          :key="coach.id"
          class="odd:bg-gray-200 border-b border-gray-400"
        >
          <td class="py-2 px-3 text-gray-700">
            <router-link :to="{ name: 'trainings.coaches.read', params: { id : coach.id} }">
              {{ coach.member.person.name }}
            </router-link>
          </td>
          <td class="py-2 px-3 text-gray-700">
            {{ coach.diploma }}
          </td>
          <td class="py-2 px-3 text-gray-700 text-center">
            <i
              v-if="coach.active"
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
              v-if="$can('update', coach)"
              class="icon-button text-gray-700 hover:bg-gray-300"
              :to="{ name : 'trainings.coaches.update', params : { id : coach.id } }"
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
import Spinner from '@/components/Spinner';
import Alert from '@/components/Alert';

import messages from './lang';

export default {
  components: {
    Spinner, Alert
  },
  i18n: messages,
  computed: {
    coaches() {
      return this.$store.state.training.coach.coaches;
    },
    noData() {
      return this.coaches && this.coaches.length === 0;
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
      this.$store.dispatch('training/coach/browse');
    }
  }
};
</script>
