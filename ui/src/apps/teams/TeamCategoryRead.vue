<template>
  <AdminPage :toolbar="toolbar">
    <Spinner v-if="$wait.is('team_category.read')" />
    <Alert
      v-if="notAllowed"
      type="danger"
    >
      {{ $t('not_allowed') }}
    </Alert>
    <Alert
      v-if="notFound"
      type="danger"
    >
      {{ $t('not_found') }}
    </Alert>
    <div
      v-if="teamCategory"
      class="p-2"
    >
      <dl>
        <dt>{{ $t('name') }}</dt>
        <dd>{{ teamCategory.name }}</dd>
        <dt>{{ $t('age') }}</dt>
        <dd>{{ teamCategory.start_age }} - {{ teamCategory.end_age }}</dd>
        <dt>{{ $t('gender') }}</dt>
        <dd>{{ gender }}</dd>
        <dt>{{ $t('competition') }}</dt>
        <dd>
          <i
            class="fas fa-check"
            :class="{
              'fa-check': teamCategory.competition,
              'fa-times': !teamCategory.competition,
              'text-red-700': !teamCategory.competition
            }"
          >
          </i>
        </dd>
        <dt>{{ $t('active') }}</dt>
        <dd>
          <i
            class="fas fa-check"
            :class="{
              'fa-check': teamCategory.active,
              'fa-times': !teamCategory.active,
              'text-red-700': !teamCategory.active
            }"
          >
          </i>
        </dd>
        <dt>{{ $t('remark') }}</dt>
        <dd>{{ teamCategory.remark }}</dd>
      </dl>
      <div class="text-xs flex flex-wrap items-baseline mt-3">
        <div class="mx-2">
          <strong>Aangemaakt:</strong> {{ teamCategory.localCreatedAt }}
        </div>
        <div class="mx-2">
          <strong>Laatst gewijzigd:</strong> {{ teamCategory.localUpdatedAt }}
        </div>
      </div>
    </div>
  </AdminPage>
</template>

<style scoped>
dl {
  display: grid;
  grid-template: auto / 10em 1fr;
  @apply border-b;
}

dt {
  @apply font-bold;
  grid-column: 1;
}

dd {
  grid-column: 2;
}

dt, dd {
  @apply m-0 px-1 py-2 border-t;
}
</style>

<script>
import messages from './lang';

import Spinner from '@/components/Spinner';
import Alert from '@/components/Alert';
import AdminPage from '@/components/AdminPage';

export default {
  i18n: messages,
  components: {
    Spinner, Alert, AdminPage
  },
  computed: {
    teamCategory() {
      return this.$store.state.team.category.active;
    },
    gender() {
      var gender = this.teamCategory.gender;
      if (gender === 0) {
        return this.$t('no_restriction');
      } else if (gender === 1) {
        return this.$t('male');
      } else {
        return this.$t('female');
      }
    },
    error() {
      return this.$store.state.team.category.error;
    },
    notAllowed() {
      return this.error && this.error.response.status === 401;
    },
    notFound() {
      return this.error && this.error.response.status === 404;
    },
    toolbar() {
      const buttons = [{
        icon: 'fas fa-list',
        route: {
          name: 'team_types.browse'
        }
      }];
      if (this.teamCategory && this.$can('update', this.teamCategory)) {
        buttons.push({
          icon: 'fas fa-edit',
          route: {
            name: 'team_categories.update',
            params: {
              id: this.teamCategory.id
            }
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
    fetchData(params) {
      this.$store.dispatch('team/category/read', { id: params.id })
        .catch((error) => {
          console.log(error);
        });
    }
  }
};
</script>
