<template>
  <AdminPage>
    <Spinner
      v-if="$wait.is('training.coaches.browse')"
      class="text-center"
    />
    <div v-else>
      <Alert
        v-if="noData"
        type="warning"
      >
        {{ $t('training.coaches.no_data') }}
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
              {{ $t('training.coaches.form.diploma.label') }}
            </th>
            <th scope="col">
              {{ $t('training.coaches.form.active.label') }}
            </th>
            <th scope="col">
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="coach in coaches"
            :key="coach.id"
          >
            <th scope="row">
              <router-link :to="{
                  name: 'trainings.coaches.read',
                  params: { id : coach.id}
                }">
                {{ coach.member.person.name }}
              </router-link>
            </th>
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
                class="fas fa-times text-red-500"
              >
              </i>
            </td>
            <td>
              <router-link
                v-if="$can('update', coach)"
                class="icon-button text-gray-700 hover:bg-gray-300"
                :to="{
                  name: 'trainings.coaches.update',
                  params: {
                    id: coach.id
                  }
                }">
                  <i class="fas fa-edit"></i>
              </router-link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AdminPage>
</template>

<script>
import Spinner from '@/components/Spinner';
import Alert from '@/components/Alert';
import AdminPage from '@/components/AdminPage';

import messages from './lang';

export default {
  components: {
    Spinner, Alert, AdminPage
  },
  i18n: messages,
  computed: {
    coaches() {
      return this.$store.state.training.coach.all;
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
