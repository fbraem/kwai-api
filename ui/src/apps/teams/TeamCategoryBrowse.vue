<template>
  <AdminPage :toolbar="toolbar">
    <Spinner v-if="$wait.is('team_category.browse')" />
    <Alert
      v-else-if="noCategories"
      type="warning"
    >
      {{ $t('no_categories') }}
    </Alert>
    <table
      v-else
      class="table table-striped"
    >
      <thead>
        <th scope="col">{{ $t('name') }}</th>
      </thead>
      <tbody>
        <tr
          v-for="category in categories"
          :key="category.id"
          class="p-4 w-full md:w-1/2"
        >
          <td>
            <router-link
              :to="{
                name: 'team_categories.read',
                params: {
                  id: category.id
                }
              }"
            >
              {{ category.name }}
            </router-link>
          </td>
        </tr>
      </tbody>
    </table>
  </AdminPage>
</template>

<script>
import messages from './lang';

import Spinner from '@/components/Spinner';
import Alert from '@/components/Alert';
import AdminPage from '@/components/AdminPage';

import TeamCategory from '@/models/TeamCategory';

export default {
  i18n: messages,
  components: {
    Spinner,
    Alert,
    AdminPage
  },
  computed: {
    categories() {
      return this.$store.state.team.category.all;
    },
    noCategories() {
      return this.categories && this.categories.length === 0;
    },
    toolbar() {
      const buttons = [];
      if (this.$can('create', TeamCategory.type())) {
        buttons.push({
          icon: 'fas fa-plus',
          route: {
            name: 'team_categories.create'
          }
        });
      }
      return buttons;
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
      this.$store.dispatch('team/category/browse');
    }
  }
};
</script>
