<template>
  <!-- eslint-disable max-len -->
  <div class="page-container">
    <Spinner v-if="$wait.is('training.coaches.browse')" />
    <div
      v-else
      style="grid-column: span 2;"
    >
      <div
        v-if="noData"
        class="kwai-alert kwai-theme-warning"
      >
        {{ $t('training.coaches.no_data') }}
      </div>
      <table
        v-else
        class="kwai-table kwai-table-small kwai-table-divider kwai-table-middle"
      >
        <tr>
          <th>
            {{ $t('name') }}
          </th>
          <th>
            {{ $t('training.coaches.form.diploma.label') }}
          </th>
          <th>
            {{ $t('training.coaches.form.active.label') }}
          </th>
          <th></th>
        </tr>
        <tr
          v-for="coach in coaches"
          :key="coach.id"
        >
          <td>
            <router-link :to="{ name: 'trainings.coaches.read', params: { id : coach.id} }">
              {{ coach.member.person.name }}
            </router-link>
          </td>
          <td>
            {{ coach.diploma }}
          </td>
          <td>
            <i
              v-if="coach.active"
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
              v-if="$can('update', coach)"
              class="kwai-icon-button"
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
import messages from './lang';

export default {
  components: {
    Spinner
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
